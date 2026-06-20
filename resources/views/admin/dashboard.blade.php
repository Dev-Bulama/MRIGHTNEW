@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'System overview and management')

@section('content')
@include('partials.dashboard.admin')
@endsection