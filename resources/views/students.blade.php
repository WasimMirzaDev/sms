@extends('layouts.app')

@section('content')
@php
$route_prefix = "students.";
@endphp
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<style media="screen">
#students_menu{
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
                <h2>Student</h2>
                <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
             </header>
             <div role="content" style="display: block;">
                <div class="jarviswidget-editbox">
                </div>
                <div class="widget-body no-padding">
                   <ul class="nav nav-tabs">
                     <li class="{{empty($r->id) && empty(old('firstname')) && empty($r->copy) ? 'active' : '' }}"><a data-toggle="tab" href="#list">List</a></li>
                      <li class="{{!empty($r->id) || !empty(old('firstname')) ? 'active' : '' }}"><a data-toggle="tab" id="addnew" href="#add">Add New</a></li>
                   </ul>
                   <div class="tab-content">
                      <div id="add" class="tab-pane fade {{!empty($r->id) || !empty(old('firstname')) || !empty($r->copy) ? 'in active' : '' }} ">
                        <form method="post" id="dataForm2" class="smart-form" autocomplete="off" enctype="multipart/form-data" action="{{route($route_prefix.'save')}}">
                           <input type="hidden" name="_token" value="{{ csrf_token() }}">
                           <input type="hidden" name="id" value="{{!empty($r->id) ? $r->id : 0}}">
                           <input type="hidden" id="route_prefix" name="" value="{{url('students/')}}">
                        <fieldset>
                          <div class="row">
                            <div class="col col-md-5">
                              <div class="row" style="margin-top:5px;">
                                 <section class="col col-6">
                                   <label for="name" class="label" style="font-weight:bold;">Salutation:</label>
                                   <label class="input">
                                     <input type="text" name="salutation" id="salutation" value="{{!empty($r->salutation) ? $r->salutation : old('salutation')}}" autocomplete="off" placeholder="Salutation">
                                   </label>
                                 </section>
                                 @if(auth()->user()->role == 1)
                                 <section class="col col-6">
                                   <label for="name" class="label" style="font-weight:bold;">Dojo:<span style="color:red;">*</span></label>
                                   <select class="select2" name="dojo_id">
                                     <option value="">Select Dojo</option>
                                     @if(!empty($dojos))
                                       @foreach($dojos as $dojo)
                                         <option {{isset($r->dojo_id) && $r->dojo_id == $dojo->id ? 'selected' : ''}} value="{{$dojo->id}}">{{$dojo->name}}</option>
                                       @endforeach
                                     @endif
                                   </select>
                                 </section>
                                 @endif
                               </div>

                              <div class="row">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">First Name:<span style="color:red;">*</span> </label>
                                  <label class="input">
                                    <input type="text" name="firstname" id="firstname" value="{{!empty($r->firstname) ? $r->firstname : old('firstname')}}" autocomplete="off" placeholder="First Name">
                                  </label>
                                </section>
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Last Name:<span style="color:red;">*</span></label>
                                  <label class="input">
                                    <input type="text" name="lastname" id="lastname" value="{{!empty($r->lastname) ? $r->lastname : old('lastname')}}" autocomplete="off" placeholder="Last Name">
                                  </label>
                                </section>
                              </div>

                              <div class="row">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Birthdate:<span style="color:red;">*</span></label>
                                  <label class="input">
                                    <input type="text" class="mydatepicker" name="birthdate" id="birthdate" value="{{!empty($r->birthdate) ? date('d-m-Y', strtotime($r->birthdate)) : old('birthdate')}}" autocomplete="off" placeholder="Birthdate">
                                  </label>
                                </section>
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Email:<span style="color:red;">*</span></label>
                                  <label class="input">
                                    <input type="text" name="email" id="email" value="{{!empty($r->user->email) ? $r->user->email : old('email')}}" autocomplete="off" placeholder="Email">
                                  </label>
                                </section>
                              </div>

                              <div class="row">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Login:<span style="color:red;">*</span></label>
                                  <label class="input">
                                    <input type="text" name="login" id="login" value="{{!empty($r->user->login) ? $r->user->login : old('login')}}" autocomplete="off" placeholder="Login">
                                  </label>
                                </section>
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Password:<span style="color:red;">*</span></label>
                                  <label class="input">
                                    <input type="password" name="password" id="password" value="{{!empty($r->id) ? $r->password : ''}}" autocomplete="new-password" autocomplete="off" placeholder="Password">
                                  </label>
                                </section>
                              </div>

                              <hr style="border-top:1px dotted blue;">
                              <br>

                              <div class="row" style="margin-top:5px;">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Country:</label>
                                  <select class="select2" name="rank_id">
                                    <option value="Canada">Canada</option>
                                  </select>
                                </section>

                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Province:</label>
                                    @php
                                     $provinceList['Alberta'] = 'Alberta';
                                     $provinceList['British Columbia'] = 'British Columbia';
                                     $provinceList['Manitoba'] = 'Manitoba';
                                     $provinceList['Newfoundland and Labrador'] = 'Newfoundland and Labrador';
                                     $provinceList['New Brunswick'] = 'New Brunswick';
                                     $provinceList['Northwest Territories'] = 'Northwest Territories';
                                     $provinceList['Nova Scotia'] = 'Nova Scotia';
                                     $provinceList['Nunavut'] = 'Nunavut';
                                     $provinceList['Ontario'] = 'Ontario';
                                     $provinceList['Prince Edward Island'] = 'Prince Edward Island';
                                     $provinceList['Quebec'] = 'Quebec';
                                     $provinceList['Saskatchewan'] = 'Saskatchewan';
                                     $provinceList['Yukon'] = 'Yukon';
                                    @endphp
                                    <select class="select2" name="province">
                                      <option value="">Select Province</option>
                                      @foreach($provinceList as $prv)
                                      <option {{isset($r->province) && $r->province == $prv ? 'selected' : ''}} value="{{$prv}}">{{$prv}}</option>
                                      @endforeach
                                    </select>
                                </section>
                               </div>

                                <div class="row" style="margin-top:5px;">
                                  <section class="col col-6">
                                    <label for="name" class="label" style="font-weight:bold;">City:</label>
                                    <label class="input">
                                      <input type="text" name="city" id="city" value="{{!empty($r->city) ? $r->city : old('city')}}" autocomplete="off" placeholder="City">
                                    </label>
                                  </section>
                                     <section class="col col-6">
                                       <label for="name" class="label" style="font-weight:bold;">Postal:</label>
                                       <label class="input">
                                         <input type="text" name="postal" id="postal" value="{{!empty($r->postal) ? $r->postal : old('postal')}}" autocomplete="off" placeholder="Postal">
                                       </label>
                                     </section>
                                 </div>
                                 <div class="row">
                                   <section class="col col-12">
                                     <label for="name" class="label" style="font-weight:bold;">Address:</label>
                                     <label class="input">
                                       <textarea name="address" rows="2" class="form-control" cols="80" placeholder="Address..."> {{!empty($r->address) ? $r->address : old('address')}}</textarea>
                                     </label>
                                   </section>
                                 </div>
                                 <div class="row">
                                   <section class="col col-md-12">
                                     @if(!empty($student_log))
                                     <b>Student Log:</b>
                                     <div style="height:300px; border:1px dotted grey; overflow-y:scroll; overflow-x:hidden;">
                                         <table class="table table-striped">
                                           <tr>
                                             <td>Date</td>
                                             <td>Entry</td>
                                           </tr>
                                           @foreach($student_log as $log)
                                           <tr>
                                             <td>{{date('d-m-Y', strtotime($log->attime))}}</td>
                                             <td>{{$log->attime}}</td>
                                           </tr>
                                           @endforeach
                                         </table>
                                     </div>
                                     @endif
                                   </section>
                                 </div>
                            </div>
                            <div class="col col-md-1">

                            </div>
                            <div class="col col-md-6">
                              <div class="row">
                                <section class="col col-8">

                                </section>
                                <section class="col col-4">
                                  <div style="float:right;">
                                    <span class="input-group-btn" style="display:none;">
                                    <span class="btn btn-default btn-file">
                                       Browse… <input type="file" name="file" style="display:none;" id="imgInp" onchange="readURL(this);">
                                    </span>
                                    </span>
                                    <input type="text" id="browse_img" class="form-control bg-white" style="display:none;" name="" value="" disabled>
                                    <label for="imgInp" class="col-lg-4 text-center" style="color:black;width:110px; height:120px; display:block;">
                                    <img id="blah" src="{{!empty($r->id) && file_exists('uploads/students/'.$r->id.$r->filetype) ? asset('/uploads/students/'.$r->id.$r->filetype) : asset('/app_images/default.jpg')}}" alt="" style="=cursor:pointer;max-width:100%;max-height:100%;" class="img-fluid"/>
                                    Student Image (jpg/png)
                                    </label>
                                  </div>

                                </section>
                              </div>
                              <div class="row">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Rank:</label>
                                  <select class="select2" name="rank_id">
                                    <option value="">Select Rank</option>
                                    @if(!empty($ranks))
                                      @foreach($ranks as $rank)
                                        <option {{isset($r->rank_id) && $r->rank_id == $rank->id ? 'selected' : ''}} value="{{$rank->id}}">{{$rank->name}}</option>
                                      @endforeach
                                    @endif
                                  </select>
                                </section>
                              </div>

                              <div class="row" style="margin-top:5px;">
                                   <section class="col col-6">
                                     <label for="name" class="label" style="font-weight:bold;">Program:</label>
                                     <select class="select2" name="program_id">
                                       <option value="">Select Program</option>
                                       @if(!empty($programs))
                                         @foreach($programs as $program)
                                           <option {{isset($r->program_id) && $r->program_id == $program->id ? 'selected' : ''}} value="{{$program->id}}">{{$program->name}}</option>
                                         @endforeach
                                       @endif
                                     </select>
                                   </section>
                                   <section class="col col-3">
                                     <label for="name" class="label" style="font-weight:bold;">Start Date:</label>
                                     <label class="input">
                                       <input type="text" class="mydatepicker" name="startdate" id="startdate" value="{{!empty($r->startdate) ? date('d-m-Y', strtotime($r->startdate)) : old('startdate')}}" autocomplete="off" placeholder="Start Date">
                                     </label>
                                   </section>
                                   <section class="col col-3">
                                     <label for="name" class="label" style="font-weight:bold;">Expiry:</label>
                                     <label class="input">
                                       <input type="text" class="mydatepicker" name="expiry" id="expiry" value="{{!empty($r->expiry) ? date('d-m-Y', strtotime($r->expiry)) : old('expiry')}}" autocomplete="off" placeholder="Expiry">
                                     </label>
                                   </section>
                               </div>


                              <div class="row" style="margin-top:5px;">
                                <section class="col col-6">
                                  <label for="name" class="label" style="font-weight:bold;">Tuition Price:</label>
                                  <label class="input">
                                    <input type="number" name="tuitionprice" id="tuitionprice" value="{{!empty($r->tuitionprice) ? $r->tuitionprice : old('tuitionprice')}}" autocomplete="off" placeholder="Tuition Price">
                                  </label>
                                </section>
                                   <section class="col col-3">
                                     <label for="name" class="label" style="font-weight:bold;">Last Exam:</label>
                                     <label class="input">
                                       <input type="text" class="mydatepicker" name="lastexam" id="lastexam" value="{{!empty($r->lastexam) ? date('d-m-Y', strtotime($r->lastexam)) : old('lastexam')}}" autocomplete="off" placeholder="Last Exam">
                                     </label>
                                   </section>
                                   <section class="col col-3">
                                     <label for="name" class="label" style="font-weight:bold;">Sex:</label>
                                      <select class="select2" name="sex">
                                        <option {{!empty($r->sex) && ucwords($r->sex) == 'M' ? 'selected' : ''}} value="m">Male</option>
                                        <option {{!empty($r->sex) && ucwords($r->sex) == 'F' ? 'selected' : ''}} value="f">Female</option>
                                      </select>
                                   </section>
                               </div>

                               <hr style="border-top:1px dotted blue;">
                               <br>

                               <div class="row" style="margin-top:5px;">
                                    <section class="col col-4">
                                      <label for="name" class="label" style="font-weight:bold;">Home Phone:</label>
                                      <label class="input">
                                        <input type="text" name="homephone" id="homephone" value="{{!empty($r->homephone) ? $r->homephone : old('homephone')}}" autocomplete="off" placeholder="Home Phone">
                                      </label>
                                    </section>
                                    <section class="col col-4">
                                      <label for="name" class="label" style="font-weight:bold;">Work Phone:</label>
                                      <label class="input">
                                        <input type="text" name="workphone" id="workphone" value="{{!empty($r->workphone) ? $r->workphone : old('workphone')}}" autocomplete="off" placeholder="Work Phone">
                                      </label>
                                    </section>
                                    <section class="col col-4">
                                      <label for="name" class="label" style="font-weight:bold;">Mobile Phone:</label>
                                      <label class="input">
                                        <input type="text" name="mobilephone" id="mobilephone" value="{{!empty($r->mobilephone) ? $r->mobilephone : old('mobilephone')}}" autocomplete="off" placeholder="Mobile Phone">
                                      </label>
                                    </section>
                                </div>

                                 <div class="row" style="margin-top:5px;">
                                    <section class="col col-4">
                                      <label for="name" class="label" style="font-weight:bold;">Emergency Contact 1:</label>
                                      <label class="input">
                                        <input type="text" name="emergcontact1" id="emergcontact1" value="{{!empty($r->emergcontact1) ? $r->emergcontact1 : old('emergcontact1')}}" autocomplete="off" placeholder="Emergency Contact 1">
                                      </label>
                                    </section>
                                    <section class="col col-4">
                                      <label for="name" class="label" style="font-weight:bold;">Emergency Contact 2:</label>
                                      <label class="input">
                                        <input type="text" name="emergcontact2" id="emergcontact2" value="{{!empty($r->emergcontact2) ? $r->emergcontact2 : old('emergcontact2')}}" autocomplete="off" placeholder="Emergency Contact 2">
                                      </label>
                                    </section>

                                    <section class="col col-md-4">
                                      <div class="inline-group">
                                        <label class="radio">
                                          <input checked {{!empty($r->id) && $r->user->active == '1' ? 'checked' : ''}} id="active" type="radio" name="active"  value="1">
                                          <i></i>Active
                                        </label>

                                        <label class="radio">
                                          <input {{!empty($r->id) && $r->user->active == '0' ? 'checked' : ''}} id="inactive" type="radio" name="active" value="0">
                                          <i></i>Inactive
                                        </label>
                                      </div>
                                    </section>
                                  </div>
                            </div>
                          </div>
                        </fieldset>
                        @if (!empty(old('firstname')))
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($message = Session::get('success'))
                         <div class="alert alert-success">
                             <p>{{ $message }}</p>
                         </div>
                     @endif
                        <footer>
                          <button type="submit" id="save_btn" class="btn btn-success">
                          Save
                          </button>
                           <a href="{{route('students.show')}}"
                           id="save_btn" class="btn btn-primary">
                           Cancel
                         </a>
                        </footer>
                      </form>
                      </div>
                      <div id="list" class="tab-pane fade {{empty($r->id) && empty(old('firstname')) && empty($r->copy) ? 'in active' : '' }}">
                         <table id="datatable_fixed_column" class="display table table-striped table-bordered" width="100%">
                            <thead>
                               <tr>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="">
                                  </th>
                                  <th class="hasinput">
                                     <input type="text" class="form-control" placeholder="" />
                                  </th>
                                  <th class="hasinput">
                                     <input class="form-control" placeholder="" type="text">
                                  </th>
                                  <th class="hasinput">
                                     <input class="form-control" placeholder="" type="text">
                                  </th>
                                  <th class="hasinput">
                                     <input class="form-control" placeholder="" type="text">
                                  </th>
                                  <th class="hasinput">
                                     <input class="form-control" placeholder="" type="text">
                                  </th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                               </tr>
                               <tr>
                                  <th>First Name</th>
                                  <th>Last Name</th>
                                  <th>Student Id</th>
                                  <th>Dojo</th>
                                  <th>Program</th>
                                  <th>Start Date</th>
                                  <th>Expiry</th>
                                  <th>Rank</th>
                                  <th>Phone</th>
                                  <th>Email</th>
                                  <th>Country</th>
                                  <th>City</th>
                                  <th>Status</th>
                                  <th>copy</th>
                                  <th>Edit</th>
                                  <th>Delete</th>
                               </tr>
                            </thead>
                            <tbody>
                               @if(!empty($list))
                                 @php $sr = 1
                                 @endphp
                                 @foreach($list as $l)
                                 <tr id="row_{{$l->id}}">
                                    <td>{{$l->firstname}}</td>
                                    <td>{{$l->lastname}}</td>
                                    <td>{{!empty($l->user->login) ? $l->user->login : ''}}</td>
                                    <td>{{!empty($l->dojo->name) ? $l->dojo->name : ''}}</td>
                                    <td>{{!empty($l->program->name) ? $l->program->name : ''}}</td>
                                    <td>{{!empty($l->startdate) ? date('d-m-Y', strtotime($l->startdate)) : ''}}</td>
                                    <td>{{!empty($l->expiry) ? date('d-m-Y', strtotime($l->expiry)) : ''}}</td>
                                    <td>{{!empty($l->rank->name) ? $l->rank->name : ''}}</td>
                                    <td>{{$l->mobilephone}}</td>
                                    <td>{{!empty($l->user->email) ? $l->user->email : ''}}</td>
                                    <td>{{$l->country}}</td>
                                    <td>{{$l->city}}</td>
                                    <td>{{!empty($l->user->active)  ? ($l->user->active == '1' ? 'Active.' : 'Inactive') : 'Inactive'}}</td>
                                    <td><a href="{{route($route_prefix.'copy')}}/{{$l->id}}"     class="btn btn-info btn-xs" ><i class="fa fa-copy"></i></a> </td>
                                    <td><a id="edit_{{$l->id}}" href="{{route($route_prefix.'edit')}}/{{$l->id}}"     class="btn btn-primary btn-xs" ><i class="fa fa-edit"></i></a> </td>
                                    <td><button type="button" id="delete_{{$l->id}}" href="{{route($route_prefix.'delete')}}/{{$l->id}}" class="btn btn-danger btn-xs"  onclick="del({{$l->id}})">X</button> </td>
                                 </tr>
                                 @endforeach
                               @endif
                            </tbody>
                         </table>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </article>
    </div>
 </section>
@endsection
