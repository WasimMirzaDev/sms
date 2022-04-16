@extends('layouts.app')
@section('content')
@php
$route_prefix = "events.";
@endphp
<style media="screen">
#event_menu{
  color:white !important;
}

     tr:hover {
          background: none !important;
        }
</style>




<section id="widget-grid" class="">
    <div class="row">
       <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
          <div class="jarviswidget jarviswidget-sortable" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false" role="widget">
             <header role="heading">
                <div class="jarviswidget-ctrls" role="menu">   <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus"></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a></div>
                <span class="widget-icon"> <i class="fa fa-check txt-color-green"></i> </span>
                <h2>Expense Type</h2>
                <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
             </header>
             <div role="content" style="display: block;">
                <div class="jarviswidget-editbox">
                </div>
                <div class="widget-body no-padding">
                        <div class="row" style="width:80%; margin:0 auto; display:block;">


                          <div class="col-sm-12 col-md-12 col-lg-12">

                            <!-- new widget -->
                            <div class="jarviswidget jarviswidget-color-blueDark">

                              <!-- widget options:
                              usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                              data-widget-colorbutton="false"
                              data-widget-editbutton="false"
                              data-widget-togglebutton="false"
                              data-widget-deletebutton="false"
                              data-widget-fullscreenbutton="false"
                              data-widget-custombutton="false"
                              data-widget-collapsed="true"
                              data-widget-sortable="false"

                              -->
                              <header>
                                <span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
                                <h2> My Events </h2>
                                <div class="widget-toolbar">
                                  <!-- add: non-hidden - to disable auto hide -->
                                  <div class="btn-group">
                                    <button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
                                      Showing <i class="fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu js-status-update pull-right">
                                      <li>
                                        <a href="javascript:void(0);" id="mt">Month</a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0);" id="ag">Agenda</a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0);" id="td">Today</a>
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                              </header>

                              <!-- widget div-->
                              <div>

                                <div class="widget-body no-padding">
                                  <!-- content goes here -->
                                  <div class="widget-body-toolbar">

                                    <div id="calendar-buttons">

                                      <div class="btn-group">
                                        <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
                                      </div>
                                    </div>
                                  </div>
                                  <div id="calendar"></div>

                                  <!-- end content -->
                                </div>

                              </div>
                              <!-- end widget div -->
                            </div>
                            <!-- end widget -->

                          </div>

                        </div>



                        <!-- Modal -->
                        <div id="event_modal" class="modal" role="dialog" data-backdrop="static" data-keyboard="false" style="margin-top:-25px;">
                          <div class="modal-dialog" id="event_modal_detail">

                          </div>
                        </div>
                </div>
             </div>
          </div>
       </article>
    </div>
 </section>

@endsection
