@extends('web::layouts.app')

@section('title', trans('fitting::fitting.settings'))
@section('page_header', trans('fitting::fitting.settings'))
@section('page_description', trans('fitting::fitting.settings'))


@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-fitting-about.css" />
@endpush


@section('content')
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">{{ trans('fitting::fitting.settings') }}</h3>
      </div>
      <div class="card-body">

       <form action="{{ route("fitting.saveSettings") }}" method="POST">
           @csrf

           <div class="form-group">
               <label for="evepraisal">EvePraisal Domain</label>
               <input type="text" name="evepraisal" id="evepraisal" value="{{setting("fitting.evepraisal.domain", true) ?? ""}}" class="form-control">
               <small class="text-muted">Enter the domain to an evepraisal instance like www.goonpraisal.com</small>
           </div>

           <button type="submit" class="btn btn-primary">Save</button>
       </form>

      </div>
    </div>
@endsection


