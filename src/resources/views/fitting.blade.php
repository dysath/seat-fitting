@extends('web::layouts.grids.12')

@section('title', trans('fitting::fitting.list'))
@section('page_header', trans('fitting::fitting.list'))

@section('full')
    <div class="box box-primary box-solid">
        <div class="box-header"><h3 class="box-title">Fitting Requests</h3></div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
	            <li class="active"><a href="#tab_1" data-toggle="tab">Fittings List</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Add a Fitting</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Check Fittings</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    Hi
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
