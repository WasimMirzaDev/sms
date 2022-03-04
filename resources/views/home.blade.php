@extends('layouts.app')
@section('content')
<style media="screen">
   #dashboard_menu{
   color:white !important;
   }
   .demo{
   top:10px !important;
   }
</style>
<link rel="stylesheet" href="{{asset('css/dashboard-tiles.css')}}">
<div class="container-fluid">
  <div class="container page-heading">
     <h1>Dashboard - Apartment Management System</h1>
  </div>
  <br/>
   <div class="row" style="display:none;">
      <div class="col-md-3">
         <a class="info-tiles tiles-green has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Receiveables</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">$0</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-blue has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Payable</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">$0</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-midnightblue has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Tenants</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_tenants}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-danger has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Complaints</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">0</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-info has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Addresses</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_buildings}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-warning has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Units</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">0/{{$total_units}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>

   </div>
</div>
</div>
@endsection
