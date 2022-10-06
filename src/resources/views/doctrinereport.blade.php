@extends('web::layouts.grids.12')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-fitting-report.css" />
@endpush


@section('full')
<div class="card card-primary card-solid">
    <div class="card-header">
        <h3 class="card-title">Doctrine Report</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="alliances">Alliance:</label>
                    <select id="alliances" class="form-control">
                        <option value="0">---</option>
                        @foreach ($alliances as $alliance)
                        <option value="{{ $alliance->alliance_id }}">{{ $alliance->name }}[{{ $alliance->ticker }}]</option>
                        @endforeach
                    </select>
                    <p class="help-block"><b>Note:</b>  Report results are (can fly ship / can fly fitted ship)</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="corporations">Corporation:</label>
                    <select id="corporations" class="form-control">
                        <option value="0">---</option>
                        @foreach ($corps as $corp)
                        <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="characters">Characters:</label>
                    <select id="characters" class="form-control">
                        <option value="0">---</option>
                        @foreach ($chars as $char)
                        <option value="{{ $char->character_id }}">{{ $char->name }}</option>
                        @endforeach
                    </select>
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
                    <span class="fa fa-sync"></span>
                    Run Report
                </button>
            </div>
        </div>
    </div>
    <div class="card-footer text-muted">
        Plugin maintained by <a href="{{ route('fitting.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>
</div>

<div class="card card-primary card-solid" id="reportbox">
    <div class="card-header bg-danger d-none" id="missing_warn">
        <h3 class="card-title">It appears that you have duplicate fitting names. This will cause the report to not function correctly. Please amend your fittings names.</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow: auto">
            <table id="report" class="table table-condensed table-striped no-footer">
            <thead>
            </thead>
            <tbody>
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('javascript')
<script type="application/javascript">
    var button = $('#runreport');
    var table;
    var report = $('#report');

    $( document ).ready(function() {
        $('#reportbox').hide();

        $('#alliances').select2({sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),});
        $('#corporations').select2({sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),});
        $('#doctrines').select2({sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),});
        $('#characters').select2({sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),});
    });

    button.on('click', function () {
        allianceid = $('#alliances').find(":selected").val();
        corpid = $('#corporations').find(":selected").val();
        doctrineid = $('#doctrines').find(":selected").val();
        charid = $('#characters').find(":selected").val();
        button.prop("disabled", true);
        button.html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
        );

        //
        // hide pane while loading data
        //
        $('#reportbox').hide();

        $('#missing_warn').addClass('d-none');
        button.removeClass("bg-danger")

        //
        // in case datatable has already been set, ensure data are cleared from cache and destroy the instance
        //
        if (table) {
            table.clear();
            table.destroy();
            report.find("thead, tbody").empty();
        }

        report.find("thead, tbody").empty();

        $.ajax({
            headers: function () {
            },
            url: "/fitting/runReport/" + allianceid + "/" + corpid + "/" + charid + "/" +  doctrineid,
            type: "GET",
            datatype: 'json',
            timeout: 60000
        }).done( function (result) {

            try {

                if (Object.keys(result.fittings).length != (Object.keys(result.totals).length - 1)) {
                    $('#missing_warn').removeClass('d-none');
                }

                header = "";

                for (fit in result.fittings) {

                    header = header + "<th style='text-align: center'>" + result.fittings[fit] + "</th>";
                }

                header = header + "</tr>";

                report.find("thead").append("<tr><th>Character</th>" + header);

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

                report.find("tbody").prepend(body);
            
                for (var char in result.chars) {
                    body = "<tr><td style='position: sticky;'>"+char+"</td>";

                    for (var ships in result.chars[char]) {
                        if (result.chars[char][ships].ship == true) {
                            body = body + "<td style='text-align: center; width: 10em; min-width: 95px;'><span class='badge badge-success'>HULL</span> / ";
                        } else {
                            body = body + "<td style='text-align: center; width: 10em; min-width: 95px;'><span class='badge badge-danger'>HULL</span> / ";
                        }

                        if (result.chars[char][ships].fit == true) {
                            body = body + "<span class='badge badge-success'>FIT</span></td>";
                        } else {
                            body = body + "<span class='badge badge-danger'>FIT</span></td>";
                        }
                    }

                    body = body + "</tr>";

                    report.find("tbody").append(body);
                }

                //
                // show report content
                //
                $('#reportbox').show();

                // table = report.DataTable({
                //     scrollX: true,
                //     // scrollY: "300px",
                //     scrollCollapse: true,
                //     paging: false,
                //     fixedColumns: true
                // });

                button.html(
                    `<span class="fa fa-sync"></span>
                        Run Report
                    </button>`
                );
                button.prop("disabled", false);

            } catch (error) {
                button.html(
                    `<span class="fa fa-sync"></span>
                        Run Report (Last Report Failed)
                    </button>`
                );
                button.addClass("bg-danger")
                button.prop("disabled", false);              
            }
        })
        .fail(function() {
            button.html(
                    `<span class="fa fa-sync"></span>
                        Run Report (Last Report Timed Out)
                    </button>`
                );
                button.addClass("bg-danger")
                button.prop("disabled", false);  
        });
    });
</script>
@endpush

