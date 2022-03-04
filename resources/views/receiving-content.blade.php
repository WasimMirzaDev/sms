<div class="modal-header alert alert-danger">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title">{{$tenant->name}}</h4>
   <small style="font-size:10px;">Rent pays {{$pays_on}}</small>
</div>
<div class="modal-body" style="padding-top:0px">

  <input type="hidden" name="id" value="{{!empty($r->id) ? $r->id : 0}}">
  <input type="hidden" id="unit_id" name="unit_id" value="{{!empty($r->unit_id) ? $r->unit_id : $request->unit_id}}">
  <input type="hidden" id="tenant_id" name="tenant_id" value="{{!empty($r->tenant_id) ? $r->tenant_id : $request->tenant_id}}">
  <input type="hidden" id="vch_id" name="vch_id" value="{{!empty($r->challan_id) ? $r->challan_id : $request->id}}">

  <div class="row">
    <section class="col col-md-2">
      Vch Num:
    </section>
    <section class="col col-md-3">
      <label class="input">
        <input type="text" class="form-control" name="vch_num" value="{{!empty($r->challan_id) ? $r->challan_id : $request->id}}" disabled readonly>
      </label>
    </section>

    <section class="col col-md-2">
      Receivable
    </section>
    <section class="col col-md-3">
      <label class="input">
        <input type="text" class="form-control" name="pending_amt" disabled readonly value="{{$rem_amt}}">
      </label>
    </section>

  </div>
  <div class="row">
    <section class="col col-md-2">
      Rec Date:
    </section>
    <section class="col col-md-3">
      <label class="input">
        <input type="text" class="form-control mydatepicker" autocomplete="off" name="date" value="{{(!empty($r->id)) ? date('d-m-Y', strtotime($r->date)) : date('d-m-Y')}}">
      </label>
    </section>
    <section class="col col-md-2" style="color:green;">
      Receiving
    </section>
    <section class="col col-md-3">
      <label class="input">
        <input type="number" class="form-control" style="color:green;" name="amount" value="{{!empty($r->challan_id) ? $r->amount : $rem_amt}}">
      </label>
    </section>
  </div>

  <div class="row">
    <section class="col col-md-2">
      Pay Via
    </section>
    <section class="col col-md-3">
        <select class="form-control" name="pm_id" id="pm_id" onchange="isCash()">
          @foreach($pm as $p)
          <option {{!empty($r->id) && $r->pm_id == $p->id ? 'selected' : '' }} is_cash="{{$p->is_cash}}" value="{{$p->id}}">{{$p->name}}</option>
          @endforeach
        </select>
    </section>

    <div class="col col-md-2">

    </div>
    <section class="col col-md-5" style="{{!empty($r->id) && $r->pm->is_cash == 0 ? 'display:block;' : 'display:none' }}"  id="cheque_no">
      <label class="input">
        <input type="text" class="form-control" name="cheque_no" placeholder="Bank/Cheque Number" value="{{!empty($r->id) ? $r->cheque_no : '' }}">
      </label>
    </section>
  </div>

    <div class="row" style="margin-top:5px;">
      <section class="col col-md-12">
        Remarks
        <label class="textarea">
          <textarea name="remarks" rows="3" cols="78">{{(!empty($r->id)) ? $r->remarks : ''}}</textarea>
        </label>
      </section>
    </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-danger" data-dismiss="modal" style="float:right">Cancel</button>
   &nbsp;&nbsp;&nbsp;
  <button class="btn btn-success" type="submit"
  onclick="save_receiving()"
  id="save_btn" style="float:right;margin-right:5px;">Receive</button>
</div>
