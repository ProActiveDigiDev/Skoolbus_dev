@php
if($getRecord()){
    $qrLink =  route('rider-ride-request', ['rider' => encID($getRecord()->id, 'enc')]);
    $qrCode =  "http://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=" . $qrLink . "&choe=UTF-8";
}
@endphp

<x-filament::section class="flex flex-col items-center justify-center">
    <div class="flex flex-col items-center justify-center">
        <img src="{{ $qrCode }}" alt="QR Code" width="150" height="150" class="mx-auto mb-3 inline-flex">
        <p class="mt-4 text-center text-sm text-gray-500">Save this Unique QR Code fo {{ $getRecord()->name }}</p>
    </div>
</x-filament::section>