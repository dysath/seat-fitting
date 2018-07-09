@extends('web::layouts.grids.12')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@section('full')
<div class="box box-primary box-solid">
    <div class="box-header">
        <h3 class="box-title">Doctrine Report</h3>
    </div>
    <div class="box-body">
    <label>Corporation:</label><select id="corporations">
        @foreach ($corps as $corp)
        <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
        @endforeach
    </select>
    <label>Doctrine:</label><select id="doctrines">
        @foreach ($doctrines as $doctrine)
        <option value="{{ $doctrine->id }}">{{ $doctrine->name }}</option>
        @endforeach
    </select>
    <button id="runreport">Run Report</button>
    <p><b>Note:</b>  Report results are (can fly ship / can fly fitted ship)</p>
    </div>
</div>

<div class="box box-primary box-solid" id="totalsbox">
    <div class="box-body">
        <table id="totals" class="table table-condensed">
        <thead>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
</div>

<div class="box box-primary box-solid" id="reportbox">
    <div class="box-body">
        <table id="report" class="table table-condensed table-striped no-footer">
        <thead>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
</div>
@endsection

@push('javascript')
<script type="application/javascript">

$( document ).ready(function() {
    $('#totalsbox').hide();
    $('#reportbox').hide();
});

$('#runreport').on('click', function () {    
    corpid = $('#corporations').find(":selected").val();
    doctrineid = $('#doctrines').find(":selected").val();

    $.ajax({
        headers: function () {
        },
        url: "/fitting/runReport/"+corpid+"/"+doctrineid,
        type: "GET",
        datatype: 'json',
        timeout: 10000
    }).done( function (result) {
       result = JSON.parse(result);
       $('#report').find("thead").empty();
       $('#report').find("tbody").empty();
       $('#totals').find("thead").empty();
       $('#totals').find("tbody").empty();
       $('#totalsbox').show();
       $('#reportbox').show();

       header = "";

       for (var fit in result.fittings) {
           header = header + "<th style='text-align: center'>" + result.fittings[fit] + "</th>";
       }
       header = header + "</tr>";
       $('#report').find("thead").append("<tr><th>Character</th>" + header);
       $('#totals').find("thead").append("<tr><th></th>" + header);
 
       body = "<tr><td><label>HULL  /  FIT Totals</label></td>";
       for (var total in result.totals) {
           if (result.totals[total].ship == null) {
               result.totals[total].ship = 0;
           }
           if (result.totals[total].fit == null) {
               result.totals[total].fit = 0;
           }
           if (total != "chars") {
             body = body + "<td style='text-align: center; width: 10em;'>" + result.totals[total].ship + "  /  " + result.totals[total].fit + "<br/>";
             body = body + Math.round((result.totals[total].ship / result.totals['chars'])*100) + "%  /  " + Math.round((result.totals[total].fit / result.totals['chars'])*100) + "%</td>";
           }
       }
 
       $('#totals').find("tbody").append(body);

       for (var char in result.chars) {
           body = "<tr><td>"+char+"</td>";
           for (var ships in result.chars[char]) {
               if (result.chars[char][ships].ship == true) {
                   body = body + "<td style='text-align: center; width: 10em;'><span class='label label-success'>HULL</span> / ";
               } else {
                   body = body + "<td style='text-align: center; width: 10em;'><span class='label label-danger'>HULL</span> / ";
               } 
               if (result.chars[char][ships].fit == true) {
                   body = body + "<span class='label label-success'>FIT</span></td>";
               } else {
                   body = body + "<span class='label label-danger'>FIT</span></td>";
               } 
           }
           body = body + "</tr>";
           $('#report').find("tbody").append(body);
       }
       $('#report').DataTable( {
           "order": [[ 0, "asc" ]],
           "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
       });
    });
});

</script>
@endpush

