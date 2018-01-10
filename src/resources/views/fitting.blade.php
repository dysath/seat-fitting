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
	            <li><a href="#tab_3" data-toggle="tab">Fittings List</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <p>Cut and Paste EFT fitting in the box below</p>
                    <form role="form" action="{{ route('fitting.saveFitting') }}" method="post">
                         {{ csrf_field() }}
                         <textarea name="eftfitting" id="eftfitting" rows="15" style="width: 100%"></textarea>
                             <div class="btn-group pull-right" role="group">
                                 <input type="button" class="btn btn-default" id="verifyfitting" value="Verify this Fitting" />
                                 <input type="submit" class="btn btn-primary" id="savefitting" value="Submit this Fitting"/>
                             </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab_3">
                    <table id='fitlist' class="table table-hover">
                    <tr>
                        <th>Fit Name</th>
                    </tr>
                    @foreach($fitlist as $fit)
                        <tr id="fitid" data-id="{{ $fit->id }}"><td>[ {{ $fit->shiptype }}, {{$fit->fitname }} ]</td></tr><br />
                    @endforeach
                    </table>
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
    <div class="box box-primary box-solid">
        <div class="box-header form-group"><h3 class="box-title" id="skill-title">Required Skills</h3></div>
        <div class="box-body">
            <div id="skills-window">
            <p id="infoblox"></p>
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
        $('#fitting-window').hide();
        $('#skills-window').hide();
        $('#savefitting').hide()

        $('#fitlist').on('click', '#fitid', function () {
          /* alert( $(this).data('id')); */
          $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
                .find('tbody')
                .empty();

          uri = "['id' => " + $(this).data('id') +"]";
          $.ajax({
              headers: function () {
              },
              url: "/fitting/getfittingbyid/"+$(this).data('id'),
              type: "GET",
              dataType: 'json',
              timeout: 10000,
          }).done( function (result) {
              $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
              .find('tbody')
              .empty();
              fillFittingWindow(result);
          });

          $.ajax({
              headers: function () {
              },
              url: "/fitting/getskillsbyfitid/"+$(this).data('id'),
              type: "GET",
              dataType: 'json',
              timeout: 10000,
          }).done( function (result) {
              if (result) {
                  $('#skills-window').show();
                  $('#skillbody').empty();
                  spinner = '<select id="characterSpinner" class="form-control">';

                  for (var toons in result.characters) {
                       spinner = spinner + '<option value="'+result.characters[toons].id+'">'+result.characters[toons].name+'</option>';
                  }
                  spinner = spinner + '</select>';
                  $('#skills-window').prepend(spinner);

                  for (var skills in result.skills) {
                      skill = result.skills[skills];
                      if (typeof result.characters[0].skill[skill.typeId] != "undefined") {
                          charskillid = result.characters[0].skill[skill.typeId].level;
                          charskillid = charskillid ? charskillid : 0;
                          rank = result.characters[0].skill[skill.typeId].rank;
                      } else {
                          charskillid = 0;
                          rank = 1;
                      }

                      graphbox = drawLevelBox2(skill.level, charskillid, skill.typeName, rank);
                      $('#skillbody').append(graphbox);
                  }
              }
          });
       });
 
        $('#verifyfitting').on('click', function () {
            $('.overlay').show();
            eftcode = {'eftfitting':$('#eftfitting').val(), '_token': '{{ csrf_token() }}'};

            $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
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
            }).done( function (result) {
                fillFittingWindow(result);
            }).fail(function (result) {
            });

            $.ajax({
                headers: function () {
                },
                url: "{{ route('fitting.postSkills') }}",
                type: "POST",
                dataType: 'json',
                data: eftcode,
                timeout: 10000,
            }).done( function (result) {
                if (result) {
                    $('#skills-window').show();
                    $('#skillbody').empty();
                    for (var skills in result) {
                        skill = result[skills];
                        graphbox = drawLevelBox2(skill.level, 0, skill.typeName);
                        $('#skillbody').append(graphbox);
                    }
                }
            }).fail(function (result) {
            });

        });

        function drawLevelBox2 (neededLevel, currentLevel, skillName, rank) {
            graph = '<tr><td>'+skillName+' (x'+rank+')</td>';
            graph = graph + '<td><div style="background-color: transparent; width: 5.5em; text-align: center; height: 1.35em; letter-spacing: 2.25px;">';

            for (var i = 0; i < currentLevel; i++) {
                graph = graph + '<span class="fa fa-square text-green" style="vertical-align: text-top"></span>';
            }
            for (var i = 0; i < (neededLevel - currentLevel); i++) {
                graph = graph + '<span class="fa fa-square text-yellow" style="vertical-align: text-top"></span>';
            }
            fill = currentLevel > neededLevel ? currentLevel : neededLevel;
            for (var i = 0; i < (5 - fill); i++) {
                graph = graph + '<span class="fa fa-square-o text-green" style="vertical-align: text-top"></span>';
            }
            graph = graph + '</div></td></tr>';
            return graph;
        }

        function drawLevelBox (neededLevel, currentLevel, skillName) {
            graph = '<tr><td>'+skillName+'</td>';
            graph = graph + '<td><table id="skilllevel" style="background-color: black; width: 64px; height: 14px; border: 2px solid black; border-collapse: collapse;"><tr>';

            for (var i = 0; i < currentLevel; i++) {
                graph = graph + '<td style="background-color: green; width: 10px; border: 2px solid black;"></td>';
            }
            for (var i = 0; i < (neededLevel - currentLevel); i++) {
                graph = graph + '<td style="background-color: yellow; width: 10px; border: 2px solid black;"></td>';
            }
            fill = currentLevel > neededLevel ? currentLevel : neededLevel;
            for (var i = 0; i < (5 - fill); i++) {
                graph = graph + '<td style="background-color: gray; width: 10px; border: 2px solid black;"></td>';
            }
            graph = graph + '</tr></table></td></tr>';
            return graph;
        }

        function fillFittingWindow (result) {
            if (result) {
                $('#fitting-window').show();
                $('#savefitting').show()
                $('#middle-header').text(result['shipname'] + ', ' + result['fitname']);
                for (var slot in result) {

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

