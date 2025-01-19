@extends('organizer.layout')
@section('content')
    
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
            <div class="col-lg-3">
                <div class="card-title">{{ __('Banquet hall') }}</div>
              </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
            <table class="table table-dark">
                <thead>
                  <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Banquet Hall Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($datas as $data)
                        <tr>
                            <th>{{$i++}}</th>
                            <td>{{$data->name}}</td>
                            <td>{!!Str::limit($data->description,50)!!}</td>
                            <td>{{$data->status}}</td>
                        </tr>
                    @endforeach
                </tbody>
              </table>
        </div>
      </div>
    </div>
  </div>
@endsection