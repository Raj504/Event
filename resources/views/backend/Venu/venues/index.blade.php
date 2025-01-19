@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}


@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Categories') }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('admin.dashboard') }}">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{ __('Venu Management') }}</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{ __('Categories') }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card-title d-inline-block">{{ __('venues') }}</div>
                    </div>

                    <div class="col-lg-3">

                    </div>

                    <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                            {{ __('Add Venu') }}</a>

                        <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete" data-href="{{ route('admin.event_management.bulk_delete_category') }}">
                            <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="table-responsive">
                            <table class="table table-striped mt-3" id="basic-datatables">
                                <thead>
                                    <tr>
                                     
                                        <th scope="col">{{ __('S.No') }}</th>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Description') }}</th>
                                        <th scope="col">{{ __('price') }}</th>
                                        <th scope="col">{{ __('Capacity') }}</th>
                                        <th scope="col">{{ __('Cancellation Policy') }}</th>
                                        <th scope="col">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allVenuData as $data)
                                    <tr>
                                        <td>
                                           {{ $data->id }}
                                        </td>
                                        <td>
                                           {{$data->name}}
                                        </td>
                                        <td>
                                            {{ strlen($data->description) > 50 ? mb_substr($data->description, 0, 50, 'UTF-8') . '...' : $data->description }}
                                        </td>
                                        <td>
                                           {{$data->price}}
                                        </td>
                     
                                        <td>
                                            {{$data->capacity}}
                                         </td>
                                         <td>
                                            {{$data->cancellation_policy}}
                                         </td>
                                        <td>
                                            <a class="btn btn-secondary btn-xs mr-1 mt-1 editBtn" href="#" data-toggle="modal" data-target="#editVenuModal_{{$data->id}}" data-id="{{ $data->id }}">
                                                <span class="btn-label">
                                                    <i class="fas fa-edit"></i>
                                                </span>
                                            </a>

                                            <form class="deleteForm d-inline-block" action="{{ route('admin.deleteVenue', ['id' => $data->id]) }}" method="post">

                                                @csrf
                                                <button type="submit" class="btn btn-danger mt-1 btn-xs deleteBtn">
                                                    <span class="btn-label">
                                                        <i class="fas fa-trash"></i>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @include('backend.Venu.venues.editVenue')

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer"></div>
        </div>
    </div>
</div>

@include('backend.Venu.venues.createVenues')


@endsection