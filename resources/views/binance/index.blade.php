@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('partial.flash')
                <div class="card">
                    <div class="card-header">
                        <div style="margin-bottom: 10px;" class="row">
                            <div class="col-lg-12">
                                <a class="btn btn-success" href="{{ url('/trade') }}">
                                    Trade
                                </a>

                                <a class="btn btn-success" href="{{ url('/account') }}">
                                    Account
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
@endsection
