@extends('web::layouts.grids.12')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@section('full')
<div class="box box-primary box-solid">
    <div class="box-header">
        <h3 class="box-title">Doctrine Report</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="corporations">Corporation:</label>
                    <select id="corporations" class="form-control">
                        @foreach ($corps as $corp)
                        <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                        @endforeach
                    </select>
                    <p class="help-block"><b>Note:</b>  Report results are (can fly ship / can fly fitted ship)</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="doctrines">Doctrine:</label>
                    <select id="doctrines" class="form-control">
                        @foreach ($doctrines as $doctrine)
                        <option value="{{ $doctrine->id }}">{{ $doctrine->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" id="runreport" class="btn btn-info btn-flat">
                    <span class="fa fa-refresh"></span>
                    Run Report
                </button>
            </div>
        </div>
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
    var button = $('#runreport');
    var table;
    var report = $('#report');
    var totals = $('#totals');

    $( document ).ready(function() {
        $('#totalsbox').hide();
        $('#reportbox').hide();
    });

    button.on('click', function () {
        corpid = $('#corporations').find(":selected").val();
        doctrineid = $('#doctrines').find(":selected").val();

        button.find('span').addClass('fa-spin');

        //
        // hide pane while loading data
        //
        $('#totalsbox').hide();
        $('#reportbox').hide();

        //
        // in case datatable has already been set, ensure data are cleared from cache and destroy the instance
        //
        if (table) {
            table.clear();
            table.destroy();
            report.find("thead, tbody").empty();
        }

        totals.find("thead, tbody").empty();

        $.ajax({
            headers: function () {
            },
            url: "/fitting/runReport/" + corpid + "/" + doctrineid,
            type: "GET",
            datatype: 'json',
            timeout: 10000
        }).done( function (result) {

            header = "";

            for (fit in result.fittings) {

                header = header + "<th style='text-align: center'>" + result.fittings[fit] + "</th>";
            }

            header = header + "</tr>";

            report.find("thead").append("<tr><th>Character</th>" + header);
            totals.find("thead").append("<tr><th></th>" + header);

            body = "<tr><td><label>HULL  /  FIT Totals</label></td>";

            for (total in result.totals) {
                if (result.totals[total].ship == null) {
                    result.totals[total].ship = 0;
                }

                if (result.totals[total].fit == null) {
                    result.totals[total].fit = 0;
                }

                if (total !== "chars") {
                    body = body + "<td style='text-align: center; width: 10em;'>" + result.totals[total].ship + "  /  " + result.totals[total].fit + "<br/>";
                    body = body + Math.round((result.totals[total].ship / result.totals['chars'])*100) + "%  /  " + Math.round((result.totals[total].fit / result.totals['chars'])*100) + "%</td>";
                } 
              
            }

            totals.find("tbody").append(body);

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

                report.find("tbody").append(body);
            }

            table = report.DataTable({
                "order": [[ 0, "asc" ]],
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });

            //
            // show report content
            //
            $('#totalsbox').show();
            $('#reportbox').show();

            button.find('span').removeClass('fa-spin');
        });
    });
</script>
@endpush

