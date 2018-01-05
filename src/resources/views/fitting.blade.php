@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@section('left')
    <div class="box box-primary box-solid">
        <div class="box-header"><h3 class="box-title">Fitting Requests</h3></div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Add a Fitting</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Check Fittings</a></li>
	            <li><a href="#tab_3" data-toggle="tab">Fittings List</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <p>Cut and Paste EFT fitting in the box below</p>
                    <form role="form" action="{{ route('fitting.postFitting') }}" method="post">
                         {{ csrf_field() }}
                         <textarea id="eftfitting" rows="15" style="width: 100%"></textarea>
                             <div class="btn-group pull-right" role="group">
                                 <input type="button" class="btn btn-default" id="verifyfitting" value="Verify this Fitting" />
                                 <input type="submit" class="btn btn-primary" id="savefitting" value="Submit this Fitting"/>
                             </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab_2">
                    My
                </div>
                <div class="tab-pane" id="tab_3">
                    By
                </div>
            </div>
        </div>
    </div>
@endsection
@section('center')
    <div class="box box-primary box-solid">
        <div class="box-header"><h3 class="box-title" id='middle-header'></h3></div>
        <div class="box-body">
            <div id="fitting-window">
                 <table class="table table-condensed table-striped" id="lowSlots">
                     <thead>
                         <tr>
                             <th>Low Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="midSlots">
                     <thead>
                         <tr>
                             <th>Mid Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="highSlots">
                     <thead>
                         <tr>
                             <th>High Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="rigs">
                     <thead>
                         <tr>
                             <th>Rigs</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table id="drones" class="table table-condensed table-striped">
                     <thead>
                         <tr>
                             <th class="col-md-10">Drone Bay</th>
                             <th class="col-md-2">Number</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
            </div>
        </div>
    </div>
@endsection
@section('right')
    <div class="box box-primary box-solid">
        <div class="box-header"><h3 class="box-title"></h3></div>
        <div class="box-body">
        </div>
    </div>
@endsection

@push('javascript')
    <script type="application/javascript">
        $('#fitting-window').hide();
        $('#savefitting').hide()

        $('#verifyfitting').on('click', function () {
            $('.overlay').show();
            eftcode = {'eftfitting':$('#eftfitting').val(), '_token': '{{ csrf_token() }}'};

            $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones')
                .find('tbody')
                .empty();
            $.ajax({
                headers: function () {
                },
                url: "{{ route('fitting.postFitting') }}",
                type: "POST",
                dataType: 'json',
                data: eftcode,
                timeout: 10000,
            }).done(function (result) {
                if (result) {
                    $('#fitting-window').show();
                    $('#savefitting').show()
                    $('#middle-header').text(result['shipname'] + ', ' + result['fitname']);
                    for (var slot in result) {

                        if (slot.indexOf('HiSlot') >= 0)
                            $('#highSlots').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                        if (slot.indexOf('MedSlot') >= 0)
                            $('#midSlots').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                        if (slot.indexOf('LoSlot') >= 0)
                            $('#lowSlots').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                        if (slot.indexOf('RigSlot') >= 0)
                            $('#rigs').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                        if (slot.indexOf('dronebay') >= 0) {
                            for (item in result[slot])
                                $('#drones').find('tbody').append(
                                    "<tr><td><img src='https://image.eveonline.com/Type/" + item + "_32.png' height='16' />" + result[slot][item].name + "</td><td>" + result[slot][item].qty + "</td></tr>");
                        }
                    }
                }
            }).fail(function (result) {
            });
        });


    </script>
@endpush

