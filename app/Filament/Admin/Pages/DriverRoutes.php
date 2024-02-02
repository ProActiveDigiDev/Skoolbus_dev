<?php

namespace App\Filament\Admin\Pages;

use App\Models\Rider;
use App\Models\BusRoute;
use Filament\Pages\Page;
use App\Models\UserBooking;

class DriverRoutes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.driver-routes';

    public ?array $routes = [];

    public function mount()
    {
        return redirect()->route("user-login");
        if (!auth()->check() || !auth()->user()->hasRole(['super_admin', 'admin_user', 'driver_user'])) {
            return redirect()->route("user-login");
        }

        $this->routes = $this->getDriverRoutes();
        $this->routes = $this->addRouteInfoToRoute($this->routes);
        $this->routes = $this->addRidersToRoute($this->routes);
        $this->routes = $this->sortDriverRoutes($this->routes);
        // dd($this->routes);

    }

    public function getDriverRoutes()
    {
        $driver_routes = UserBooking::where('busroute_date', today()->format('Y-m-d'))
        // ->where('busroute_driver', auth()->user()->id)
        ->where('busroute_status', 'intransit')
        ->get()
        ->toArray();

        return $driver_routes = $driver_routes ? $driver_routes : null;
    }

    public function getRouteInfo($route_id)
    {
        //get busroute name, timeslot, from location, to location
        $route_info = BusRoute::where('id', $route_id)
        ->with([
            'fromLocation' => function ($query) {
                $query->select('id', 'name', 'location');
            },
            'toLocation' => function ($query) {
                $query->select('id', 'name', 'location');
            },
            'timeslot' => function ($query) {
                $query->select('id', 'name', 'departure_time');
            },
        ])
        ->select('id', 'name', 'from_location_id', 'to_location_id', 'timeslot_id') // Add other columns from the 'busroutes' table
        ->get()
        ->first()
        ->toArray();

        return $route_info = $route_info ? $route_info : null;
    }

    public function addRouteInfoToRoute($routes)
    {
        if($routes == null){
            return null;
        }

        foreach ($routes as $key => $route) {
            $route_info = $this->getRouteInfo($route['busroute_id']);
            $routes[$key]['route_info'] = $route_info;
        }

        return $routes;
    }

    public function sortDriverRoutes($driver_routes)
    {
        if($driver_routes == null){
            return null;
        }

        $sorted_routes = [];

        foreach ($driver_routes as $route) {
            $sorted_routes[$route['busroute_id']][] = $route;
        }

        return $sorted_routes;
    }

    public function getRider($rider_id){
        $rider = Rider::where('id', $rider_id)
        ->with([
            'schoolLocation' => function ($query) {
                $query->select('id', 'name');
            },  
        ])
        ->select('name', 'surname', 'avatar', 'school')
        ->get()
        ->first()
        ->toArray();

        return $rider = $rider ? $rider : null;
    }

    public function addRidersToRoute($routes)
    {
        if($routes == null){
            return null;
        }
        
        foreach ($routes as $key => $route) {
            $rider_info = $this->getRider($route['rider_id']);
            $routes[$key]['rider_info'] = $rider_info;
        }
        
        return $routes;        
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
        return auth()->user()->hasRole(['super_admin', 'driver_user']);
    }
}
