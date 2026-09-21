@php
    $componentName = $page['component'] ?? '';
@endphp

@if ($componentName === 'AccessibilityGuide')
    @include('skeletons.accessibility-guide')
@elseif ($componentName === 'Blog' || $componentName === 'BlogDetail')
    @include('skeletons.blog')
@elseif ($componentName === 'TripDetail')
    @include('skeletons.trip-detail')
@elseif ($componentName === 'Account')
    @include('skeletons.account')
@else
    @include('skeletons.welcome')
@endif
