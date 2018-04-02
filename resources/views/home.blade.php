@extends('layouts.app')

@section('content')
    @each('project', $projects, 'project')
@endsection