@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-fitting-fitting.css" />
@endpush

@section('left')
    <div class="card card-primary card-solid">
        <div class="card-header">
           <h3 class="card-title">Fittings</h3>
           @can('fitting.create')
           <div class="card-tools pull-right">
               <button type="button" class="btn btn-xs btn-tool" id="addFitting" data-toggle="tooltip" data-placement="top" title="Add a new fitting">
                   <span class="fa fa-plus-square"></span>
               </button>
           </div>
           @endcan
        </div>
        <div class="card-body">
        <table id='fitlist' class="table table-hover" style="vertical-align: top">
            <thead>
            <tr>
                <th></th>
                <th>Ship</th>
                <th>Fit Name</th>
                <th class="pull-right">Option</th>
             </tr>
             </thead>
             <tbody>
             @if (count($fitlist) > 0)
             @foreach($fitlist as $fit)
             <tr class="fitid" data-id="{{ $fit['id'] }}">
                 <td><img src='https://image.eveonline.com/Type/{{ $fit['typeID'] }}_32.png' height='24' /></td>
                 <td>{{ $fit['shiptype'] }}</td>
                 <td>{{ $fit['fitname'] }}</td>
                 <td class="no-hover pull-right" style="min-width:80px">
                     <button type="button" id="viewfit" class="btn btn-xs btn-success" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="View Fitting">
                         <span class="fa fa-eye text-white"></span>
                     </button>
                     @can('fitting.create')
                     <button type="button" id="editfit" class="btn btn-xs btn-warning" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="Edit Fitting">
                         <span class="fas fa-edit text-white"></span>
                     </button>
                     <button type="button" id="deletefit" class="btn btn-xs btn-danger" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="Delete Fitting">
                         <span class="fa fa-trash text-white"></span>
                     </button>
                     @endcan
                 </td>
             </tr>
             @endforeach
             @endif
             </tbody>
        </table>
        </div>
        <div class="card-footer text-muted">
            Plugin maintained by <a href="{{ route('fitting.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
        </div>
    </div>

    <div class="card card-primary card-solid" id='eftexport'>
        <div class="card-header">
           <h3 class="card-title">EFT Fitting</h3>
        </div>
        <div class="card-body">
            <textarea name="showeft" id="showeft" rows="15" style="width: 100%" onclick="this.focus();this.select()" readonly="readonly"></textarea>
        </div>
        <div class="card-footer">
        Current Jita Price (Buy /Sell)
        <a id="current_appraisal" class="float-right">
        ?/? (ISK)
        </a>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="fitEditModal">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header bg-primary">
             <h4 class="modal-title">Are you sure?</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           </div>
           <form role="form" action="{{ route('fitting.saveFitting') }}" method="post">
               <input type="hidden" id="fitSelection" name="fitSelection" value="0">
               <div class="modal-body">
                   <p>Cut and Paste EFT fitting in the box below</p>
                   {{ csrf_field() }}
                   <textarea name="eftfitting" id="eftfitting" rows="15" style="width: 100%"></textarea>
               </div>
               <div class="modal-footer">
                   <div class="btn-group pull-right" role="group">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                       <input type="submit" class="btn btn-primary" id="savefitting" value="Submit Fitting" />
                   </div>
              </div>
           </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="fitConfirmModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 class="modal-title">Are you sure?</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this fitting?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="deleteConfirm" data-dismiss="modal">Delete Fitting</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection
@section('center')
    <div class="card card-primary card-solid" id="fitting-box">
        <div class="card-header"><h3 class="card-title" id='middle-header'></h3></div>
        <input type="hidden" id="fittingId" value="" />
        <div class="card-body">
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
                  <table class="table table-condensed table-striped" id="subSlots">
                     <thead>
                         <tr>
                             <th>Subsystems</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
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
    <div class="card card-primary card-solid" id="skills-box">
        <div class="card-header form-group"><h3 class="box-title" id="skill-title">Required Skills</h3></div>
        <div class="card-body">
            <div id="skills-window">
            <table class="table table-condensed">
            <tr>
            <td><span class="fa fa-square " style="color: #5ac597"></span> Required Level</td><td><span class="fa fa-square text-green"></span> Exceeded</td>
            <td><span class="fa fa-square-o text-danger"></span> Missing Level</td> <td><span class="fa fa-square-o text-green"></span> Empty Level</td>
            </tr>
            </table>
            <select id="characterSpinner" class="form-control"></select>
            <table style="width: 100%" class="table table-condensed table-striped">
                <thead>
                    <tr>
                      <th>Skill Name</th>
                      <th style="width: 80px">Level</th>
                    </tr>
                </thead>
                <tbody id="skillbody">
                </tbody>
            </table>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
<script type="application/javascript">
    var skills_informations;

    $('#fitting-box').hide();
    $('#skills-box').hide();
    $('#eftexport').hide();
    $('#showeft').val('');

    $('#fitlist').DataTable();

    $('#addFitting').on('click', function () {
        $('#fitEditModal').modal('show');
        $('#fitSelection').val('0');
        $('textarea#eftfitting').val('');
    });

    $('#fitlist').on('click', '#deletefit', function () {
        $('#fitConfirmModal').modal('show');
        $('#fitSelection').val($(this).data('id'));
    }).on('click', '#editfit', function () {
        id = $(this).data('id');
        $('#fitEditModal').modal('show');
        $('#fitSelection').val(id);

        $.ajax({
            headers: function () {
            },
            url: "/fitting/geteftfittingbyid/" + id,
            type: "GET",
            datatype: 'string',
            timeout: 10000
        }).done( function (result) {
          $('textarea#eftfitting').val(result);
        }).fail( function(xmlHttpRequest, textStatus, errorThrown) {
        });
    }).on('click', '#viewfit', function () {
        uri = "['id' => " + $(this).data('id') +"]";
        $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
            .find('tbody')
            .empty();
        $('#fittingId').text($(this).data('id'));

        $.ajax({
            headers: function () {
            },
            url: "/fitting/getfittingbyid/"+$(this).data('id'),
            type: "GET",
            dataType: 'json',
            timeout: 10000
        }).done( function (result) {
            $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
                .find('tbody')
                .empty();
            $('#showeft').val('');
            $('#fitting-box').show();

            fillFittingWindow(result);
        });

        $.ajax({
            headers: function () {
            },
            url: "/fitting/getskillsbyfitid/"+$(this).data('id'),
            type: "GET",
            dataType: 'json',
            timeout: 10000
        }).done( function (result) {
            if (result) {
                skills_informations = result;

                $('#skills-box').show();
                $('#skillbody').empty();

                if ($('#characterSpinner option').size() === 0) {
                    for (var toons in result.characters) {
                        $('#characterSpinner').append('<option value="'+result.characters[toons].id+'">'+result.characters[toons].name+'</option>');
                    }
                }

                fillSkills(result);
            }
        });

        $.ajax({
            headers :function () {
            },
            url: "/fitting/getfittingcostbyid/"+$(this).data('id'),
            type: "GET",
            dataType: 'json',
            timeout: 10000

        }).done( function (result) {
            if (result){
                $buy = result.appraisal.totals.buy.toLocaleString();
                $sell = result.appraisal.totals.sell.toLocaleString();
                
                $('#current_appraisal').html($buy + " / " + $sell + " (ISK)");
            }
        });
    });

    $('#deleteConfirm').on('click', function () {
       id = $('#fitSelection').val();
        $('#fitlist .fitid[data-id="'+id+'"]').remove();

        $.ajax({
            headers: function () {
            },
            url: "/fitting/delfittingbyid/" + id,
            type: "GET",
                datatype: 'json',
            timeout: 10000
        }).done( function (result) {
            $('#fitlist .fitid[data-id="'+id+'"]').remove();
        }).fail( function(xmlHttpRequest, textStatus, errorThrown) {
        });
    });

    $('#characterSpinner').change( function () {
        if (skills_informations) {
            $('#skills-box').show();
            $('#skillbody').empty();

            fillSkills(skills_informations);
        }
    });

    function fillSkills (result) {

        characterId = $('#characterSpinner').find(":selected").val();

        for (skills in result.skills) {
            skill = result.skills[skills];

            if (typeof result.characters[characterId].skill[skill.typeId] !== "undefined") {
                charskilllvl = result.characters[characterId].skill[skill.typeId].level;
                rank = result.characters[characterId].skill[skill.typeId].rank;
            }

            graphbox = drawLevelBox(skill.level, charskilllvl, skill.typeName, rank);
            $('#skillbody').append(graphbox);
        }
    }

    function formatTime (points) {
      if (!points) {
          return;
      }

      hours = points / 1800;
      return parseInt(hours/24) + 'd ' + parseInt(hours%24) + 'h ' + parseInt(((hours%24) - parseInt(hours%24))*60) + 'm';
    }

    function drawLevelBox (neededLevel, currentLevel, skillName, rank) {
        if (currentLevel === 0) {
          row = '<tr class="bg-red">';
          trainingtime = formatTime(rank * 250 * Math.pow(5.66, (neededLevel-1)));
        } else if ((neededLevel - currentLevel) > 0) {
          row = '<tr class="bg-orange">';
          pointdiff = (rank * 250 * Math.pow(5.66, (neededLevel-1))) - (rank * 250 * Math.pow(5.66, (currentLevel-1))) ;
          trainingtime = formatTime(pointdiff);
        } else {
          row = '<tr>';
          trainingtime = '';
        }

        graph = row + '<td>'+skillName+' <small>(x'+rank+')</small></td>';
        graph = graph + '<td style="width: 11em"><div style="background-color: transparent; width: 5.5em; text-align: center; height: 1.35em; letter-spacing: 2.25px;" class="pull-right">';

        if (currentLevel >= neededLevel) {
            for (i = 0; i < neededLevel; i++) {
                graph = graph + '<span class="fa fa-square " style="vertical-align: text-top; color: #5ac597;"></span>';
            }
            for (i = neededLevel; i < currentLevel; i++) {
                graph = graph + '<span class="fa fa-square text-green" style="vertical-align: text-top"></span>';
            }
            for (i = 0; i < (5 - currentLevel); i++) {
                graph = graph + '<span class="fa fa-square-o text-green" style="vertical-align: text-top"></span>';
            }
        } else {
            for (i = 0; i < currentLevel; i++) {
                graph = graph + '<span class="fa fa-square " style="vertical-align: text-top; color: #5ac597;"></span>';
            }
            for (i = 0; i < (neededLevel - currentLevel); i++) {
                graph = graph + '<span class="fa fa-square-o text-danger" style="vertical-align: text-top"></span>';
            }
            for (i = 0; i < (5 - neededLevel) ; i++) {
                graph = graph + '<span class="fa fa-square-o text-green" style="vertical-align: text-top"></span>';
            }
        }

        graph = graph + '</div><span class="pull-right"><small>' + trainingtime  + '</small> </span></td></tr>';

        return graph;
    }

    function fillFittingWindow (result) {
        if (result) {
            $('#fitting-window').show();
            $('#middle-header').text(result.shipname + ', ' + result.fitname);
            $('#showeft').val(result.eft);
            $('#eftexport').show();

            for (slot in result) {

                if (slot.indexOf('HiSlot') >= 0)
                    $('#highSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");

                if (slot.indexOf('MedSlot') >= 0)
                    $('#midSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");

                if (slot.indexOf('LoSlot') >= 0)
                    $('#lowSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");

                if (slot.indexOf('RigSlot') >= 0)
                    $('#rigs').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");

                if (slot.indexOf('SubSlot') >= 0)
                    $('#subSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");

                if (slot.indexOf('dronebay') >= 0) {
                    for (item in result[slot])
                        $('#drones').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + item + "_32.png' height='24' /> " + result[slot][item].name + "</td><td>" + result[slot][item].qty + "</td></tr>");
                    }
            }
        }
    }
</script>
@endpush

