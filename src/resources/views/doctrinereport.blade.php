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

<div class="box box-primary box-solid">
    <div class="box-body">
        <table id="report" class="table table-condensed">
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
       header = "<tr><th>Character</th>";
       for (var fit in result.fittings) {
           header = header + "<th style='text-align: center'>" + result.fittings[fit] + "</th>";
       }
       header = header + "</tr>";
       $('#report').find("thead").append(header);

       for (var char in result.chars) {
           body = "<tr><td>"+char+"</td>";
           for (var ships in result.chars[char]) {
               if (result.chars[char][ships].ship == true) {
                   body = body + "<td style='text-align: center; width: 10em;'>yes/";
               } else {
                   body = body + "<td style='text-align: center; width: 10em;'>no/";
               } 
               if (result.chars[char][ships].fit == true) {
                   body = body + "yes</td>";
               } else {
                   body = body + "no</td>";
               } 
           }
           body = body + "</tr>";
           $('#report').find("tbody").append(body);
       }
    });
});

</script>
@endpush

