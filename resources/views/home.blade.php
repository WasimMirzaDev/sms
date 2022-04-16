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
     <h1>{{auth()->user()->roles->name}} - {{auth()->user()->name}}</h1>
  </div>
  <br/>
   <div class="row">
     @if(auth()->user()->role == 1)
      <div class="col-md-3">
         <a class="info-tiles tiles-green has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Dojos</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_dojos}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-blue has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Students</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_students}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-midnightblue has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Pragrams</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_programs}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-danger has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Receivable Fee</div>
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
               <div class="text-left">Total Received Fee</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">0</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3" style="display:none;">
         <a class="info-tiles tiles-warning has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Units</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">0</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>

      @endif


      @if(auth()->user()->role == 2)
      <div class="col-md-3">
         <a class="info-tiles tiles-green has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Students</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_students}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      <div class="col-md-3">
         <a class="info-tiles tiles-midnightblue has-footer" href="#">
            <div class="tiles-heading">
               <div class="text-left">Total Pragrams</div>
            </div>
            <div class="tiles-body">
               <div class="text-center">{{$total_programs}}</div>
            </div>
            <div class="tiles-footer">
               <div class=""></div>
            </div>
         </a>
      </div>
      @endif
   </div>
</div>
</div>
@endsection
