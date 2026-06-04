@props(['title' => 'Admin', 'breadcrumbs' => null])
@include('layouts.admin', ['slot' => $slot, 'title' => $title, 'breadcrumbs' => $breadcrumbs])
