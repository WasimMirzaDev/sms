@extends('layouts.app')
@section('content')
@php
$route_prefix = "pl.";
@endphp
<style media="screen">
#pl_menu{
  color:white !important;
}
</style>

<section id="widget-grid" class="">
    <div class="row">
       <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
          <div class="jarviswidget jarviswidget-sortable" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false" role="widget">
             <header role="heading">
                <div class="jarviswidget-ctrls" role="menu">   <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus"></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a></div>
                <span class="widget-icon"> <i class="fa fa-check txt-color-green"></i> </span>
                <h2>Generate Report</h2>
                <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
             </header>
             <div role="content" style="display: block;">
                <div class="jarviswidget-editbox">
                </div>
                <div class="widget-body no-padding">
                  <form method="post" action="{{route($route_prefix.'pl')}}" target="_blank" class="smart-form">
                     <input type="hidden" name="_token" value="{{ csrf_token() }}">
                     <div class="container">
                        <fieldset>
                          <div class="row">
                            <section class="col col-3" style="color:black; font-weight:bold;">
                              From Date
                              <label class="input">
                                <input type="text" class="mydatepicker" name="from_date" value="{{date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) )}}">
                              </label>
                            </section>

                            <section class="col col-3" style="color:black; font-weight:bold;">
                              To Date
                              <label class="input">
                                <input type="text" class="mydatepicker" name="to_date" value="{{date('d-m-Y')}}">
                              </label>
                            </section>
                          </div>
                           <div class="row">
                             <section class="col col-md-2">
                               <button style="padding:4px 8px;" type="submit" class="btn btn-success" name="button">Show Report</button>
                             </section>
                           </div>
                        </fieldset>
                     </div>
                  </form>
                </div>
             </div>
          </div>
       </article>
    </div>
 </section>

@endsection
