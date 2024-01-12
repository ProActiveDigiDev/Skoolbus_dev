<?php

namespace App\Filament\User\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\UserAccount;
use App\Models\UserProfile;
use App\Models\WebsiteConfigs;
use Faker\Provider\ar_EG\Text;
use App\Models\CreditPurchases;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use App\Filament\User\Resources\UserBookingResource\Widgets\CustomerOverview;

class Account extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.user.pages.account';

    protected static ?string $navigationLabel = 'Buy Credits';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?string $title = 'Buy Credits';

    public ?array $payfastData = [];


    protected function getForms(): array
    {
        return [
            'payfastForm',
        ];
    }

    public function mount(): void
    {
        abort_unless(function(): bool
        {
            $panelId = filament()->getCurrentPanel()->getID();
    
            if($panelId === 'admin' && auth()->user()->id){
                return false;
            }else if($panelId === 'Busstop' && auth()->user()->id){
                return true;
            }
        }, 403);

        //get user profile data from user_profile table
        $userAccount = UserProfile::where('user_id', auth()->user()->id)->first();

        $payfastData = [];

        if($userAccount){
            $payfastData['user_credits'] = $userAccount->user_credits ?? 0;
        }else{
            $payfastData['user_credits'] = 0;
        }

        $userInfo = auth()->user()->user_profile;
        //add user profile data to payfastData
        $payfastData['name_first'] = $userInfo->name;
        $payfastData['name_last'] = $userInfo->surname;
        $payfastData['email_address'] = auth()->user()->email;
        $payfastData['confirmation_address'] = auth()->user()->email;
        $payfastData['cell_number'] = $userInfo->phone;

        if($payfastData){
            $this->payfastForm->fill($payfastData, 'payfastForm');
        } 
    }

    public function payfastForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('credits_purchased')
                    ->label('Credits')
                    ->numeric()
                    ->required()
                    ->columnSpan(4),

                Checkbox::make('email_confirmation')
                    ->label('Send Email Confirmation')
                    ->live()
                    ->inline(false)
                    ->columnSpan(4),

                TextInput::make('confirmation_address')
                    ->label('Confirmation Address')
                    ->email()
                    ->maxLength(100)
                    ->hidden(fn (Get $get) => !$get('email_confirmation'))
                    ->required(fn (Get $get) => $get('email_confirmation'))
                    ->columnSpan(6),

            ])
            ->columns(8)
            ->statePath('payfastData')
            ->model(UserAccount::class);;
    }


    public function submit()
    {
        $this->creditsPurchaseProcess();

        return redirect()->route('filament.Busstop.pages.account'); 
    }

    //
    public function creditsPurchaseProcess()
    {
        //get form data
        $payfastFormState = $this->payfastForm->getState();
        //get user account data
        $userAccount = UserAccount::where('user_id', auth()->user()->id)->first();
        
        //set user_id to form data
        $payfastFormState['user_id'] = auth()->user()->id;
        //set new user credits to form data
        $payfastFormState['current_credits'] = $userAccount->user_credits ?? 0;
        

        $formArr = $this->processPayfastFormData($payfastFormState);

        $updated = $this->saveTransaction($formArr);


        $formArr['payfastFormData'] = $this->payfastFormSubmit($formArr['payfastFormData']);


        if($formArr['payfastFormData']['payment_status'] === 'COMPLETE'){
            $updated =  $this->saveCredits($formArr, $userAccount);
        }else{
            $updated = false;;
        }

        if(!$updated){
            Notification::make()
                ->danger()
                ->title('Something went wrong, please try again')
                ->send();

        }else{
            Notification::make()
                ->success()
                ->title('Credits added successfully')
                ->send();
        }

    }

    //Process the form data before submitting
    public function processPayfastFormData($formData)
    {
        $payfastFormData =[];

        $formData['cost_per_credit_at_purchase'] = WebsiteConfigs::where('var_name', 'ride_credit_rate')->pluck('var_value')->first();

        //Set Payfast constants
        $payfastFormData['merchant_id'] = env('PAYFAST_MERCHANT_ID');
        $payfastFormData['merchant_key'] = env('PAYFAST_MERCHANT_KEY');
        
        //Set Payfast URLs only if not empty
        $payfastFormData['return_url'] = env('PAYFAST_RETURN_URL');
        $payfastFormData['cancel_url'] = env('PAYFAST_CANCEL_URL');
        $payfastFormData['notify_url'] = env('PAYFAST_NOTIFY_URL');
        
        //set user information
        $payfastFormData['name_first'] = $this->payfastData['name_first'];
        $payfastFormData['name_last'] = $this->payfastData['name_last'];
        $payfastFormData['email_address'] = $this->payfastData['email_address'];
        $payfastFormData['cell_number'] = $this->payfastData['cell_number'];
        
        //set transaction details
        $payfastFormData['m_payment_id'] = $formData['user_id'] . '-' . time();
        $payfastFormData['amount'] = $formData['credits_purchased'] * $formData['cost_per_credit_at_purchase'];
        $payfastFormData['amount'] = number_format( sprintf( '%.2f', $payfastFormData['amount'] ), 2, '.', '' );
        $payfastFormData['item_name'] = 'Ride_Credits';
        
        //set email confirmation
        if($this->payfastData['email_confirmation'])
        {
            $payfastFormData['email_confirmation'] = $this->payfastData['email_confirmation'];
            $payfastFormData['confirmation_address'] = $this->payfastData['confirmation_address'];
        }

        //add passphrase
        $payfastFormData['passphrase'] = env('PAYFAST_PASSPHRASE');

        //remove all empty and null values
        $payfastFormData = array_filter($payfastFormData);


        $returnData =  [
            'formData' => $formData, 
            'payfastFormData' => $payfastFormData
        ];

        return $returnData;
    }

    //Save the purchase to the database, update if payfastFormSubmit() is successful
    public function saveTransaction($data, $isUpdate = false)
    {        
        //get and sort form data
        $formData = $data['formData'];
        $payfastFormData = $data['payfastFormData'];

        $saveData = [];
        
        //save the purchase to the database
        if($isUpdate){
            //Set the data to be updated
            $saveData = [
                'amount_fee' => $payfastFormData['amount_fee'],
                'amount_net' => $payfastFormData['amount_net'],
                'pf_payment_id' => $payfastFormData['pf_payment_id'],
                'payment_status' => $payfastFormData['payment_status'],
            ];
            
            //update the purchase on the database
            $updated = CreditPurchases::where('m_payment_id', $payfastFormData['m_payment_id'])->update($saveData) ? true : false;
        
        }else{
            //Set the data to be saved
            $saveData =[
                'user_id' => $formData['user_id'],
                'credits_purchased' => $formData['credits_purchased'],
                'cost_per_credit_at_purchase' => $formData['cost_per_credit_at_purchase'],
                'total_amount' => $payfastFormData['amount'],
                'm_payment_id' => $payfastFormData['m_payment_id'],
                'payment_status' => 'pending',
            ];

            //save the purchase to the database
            $updated = CreditPurchases::create($saveData) ? true : false;
        }

        //return true if the transaction was saved or updated
        return $updated;
    }

    //Submit the form to payfast
    public function payfastFormSubmit($payfastData)
    {

        //set signature
        $payfastData['signature'] = $this->generatePayfastSignature($payfastData, env('PAYFAST_PASSPHRASE'));
        
        $payfastUrl = env('PAYFAST_SANDBOX_MODE') ? env('PAYFAST_SANDBOX_URL') : env('PAYFAST_LIVE_URL');               
        
        // Create a query string from the data
        $queryString = http_build_query($payfastData);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $payfastUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        
        // Execute cURL session
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        if($response){
            $response = explode('&', $response);
            $response = array_combine(array_column($response, '0'), array_column($response, '1'));
        }

        //TODO: complete the payfastFormSubmit() function
        // Process the PayFast response (parse, validate, etc.)
        // Note: PayFast will send a response to the callback URL specified in your PayFast account settings
        
        // Do additional processing based on the response

        /* ************************************** */
        //TODO: remove this line from production
        if(env('APP_ENV') === 'local'){
            $response = [
                'm_payment_id' => $payfastData['m_payment_id'],
                'pf_payment_id' => 'pf_' . $payfastData['m_payment_id'],
                'payment_status' => 'COMPLETE',
                'item_name' => 'Ride_Credits',
                'amount_gross' => $payfastData['amount'],
                'amount_fee' => (2.3 / 100) * $payfastData['amount'], 
                'amount_net' => $payfastData['amount'] - ((2.3 / 100) * $payfastData['amount']),
                'name_first' => $payfastData['name_first'],
                'name_last' => $payfastData['name_last'],
                'email_address' => $payfastData['email_address'],
                'merchant_id' => $payfastData['merchant_id'],
                'signature' => $payfastData['signature'],
               ];
        }

        //merge the payfast return data with the payfast form data
        $response = array_merge($response, $payfastData);

        return $response;
    }

    //Save the credits to the database
    public function saveCredits($data, $userAccount)
    {
        /** ************ CONTINUE HERE ********** */
        //update user credits
        $data['formData']['user_credits'] = $data['formData']['current_credits'] + $data['formData']['credits_purchased'];
        
        //update or create user account
        if($userAccount){
            $updated = $userAccount->update($data['formData']) ? true : false;
        }else{
            $updated = $userAccount = UserAccount::create($data['formData']) ? true : false;
        }

        //update transaction status
        if($updated){
            $updated = $this->saveTransaction($data, true);
        }else{
            $updated = false;
        }
        
        return $updated;

    }

    /**
     * @param array $data
     * @param null $passPhrase
     * @return string
     */
    public function generatePayfastSignature($payfastData, $passphrase = null)
    {
        // Initialize an empty array to store encoded key-value pairs
        $encodedPairs = array();

        // URL encode key-value pairs
        foreach ($payfastData as $key => $value) {
            $encodedPairs[] = $key . '=' . urlencode($value);
        }

        // Concatenate key-value pairs with '&'
        $dataString = implode('&', $encodedPairs);

        // Generate MD5 hash and convert to uppercase
        $signature = md5($dataString);

        return $signature;
    }  
       
    
    //close modal
    public function closeModal()
    {
        $this->dispatch('close-modal', id: 'credit-purchase-modal');
    }

    /**
     * Get the widgets available for the page.
     *
     * @return array
     */
    public static function getWidgets(): array
    {
        return [
            CustomerOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerOverview::class,
        ];
    }


    public static function shouldRegisterNavigation(): bool
    {
        $panelId = filament()->getCurrentPanel()->getID();

        if($panelId === 'admin'){
            return false;
        }else if($panelId === 'Busstop'){
            return true;
        }
    }
}
