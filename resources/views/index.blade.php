@extends('layouts.app')

@section('page-title', __('Activity Log'))
@section('page-heading', isset($user) ? $user->present()->nameOrEmail : __('Activity Log'))

@section('breadcrumbs')
    @if (isset($user) && isset($adminView))
        <li class="breadcrumb-item">
            <a href="{{ route('activity.index') }}">@lang('Activity Log')</a>
        </li>
        <li class="breadcrumb-item active">
            {{ $user->present()->nameOrEmail }}
        </li>
    @else
        <li class="breadcrumb-item active">
            @lang('Activity Log')
        </li>
    @endif
@stop

@section('styles')
    <link rel="stylesheet" href="{{ url('vendor/plugins/activity-log/css/activity-log.css') }}">
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="" method="GET" id="users-form" class="border-bottom-light mb-3">
                <div class="row justify-content-between mt-3 mb-4">
                    <div class="col-lg-5 col-md-6">
                        <div class="input-group custom-search-form">
                            <select class="form-control" name="search">
                                <option value="">@lang('Search for Action')</option>
                                @foreach (\Vanguard\UserActivity\Support\Enum\ActivityTypes::getConstants() as $key => $value)
                                    <option value="{{ $value }}" {{ Request::get('search') == $value ? 'selected' : '' }}>
                                        @lang($value, ['name' => '{' . __('Name') . '}' ])
                                    </option>
                                @endforeach
                            </select>
                            <select id="user-select" class="form-control user-select" name="userId"
                                    data-url="{{ route('users.list') }}"
                                    data-placeholder="@lang('Search for User')"
                                    data-selected-id="{{ $selectedUser->id ?? '' }}"
                                    data-selected-text="{{ ($selectedUser->first_name ?? '') . ' ' . ($selectedUser->last_name ?? '') }}">
                                <option value="">@lang('Search for User')</option>
                            </select>

                            <span class="input-group-append">
                            @if (Request::has('search') && Request::get('search') != '')
                                    <a href="{{ isset($adminView) ? route('activity.index') : route('profile.activity') }}"
                                       class="btn btn-light d-flex align-items-center"
                                       role="button">
                                    <i class="fas fa-times text-muted"></i>
                                </a>
                                @endif
                            <button class="btn btn-light" type="submit" id="search-activities-btn">
                                <i class="fas fa-search text-muted"></i>
                            </button>
                        </span>
                        </div>
                    </div>
                </div>
        </form>

        <div class="table-responsive">
            <table class="table table-borderless table-striped">
                <thead>
                    @if (isset($adminView))
                        <th class="min-width-150">@lang('User')</th>
                    @endif
                    <th>@lang('IP Address')</th>
                    <th class="min-width-200">@lang('Message')</th>
                    <th class="min-width-200">@lang('Log Time')</th>
                    <th class="text-center">@lang('More Info')</th>
                </thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            @if (isset($adminView))
                                <td>
                                    @if (isset($user))
                                        {{ $activity->user->present()->nameOrEmail }}
                                    @else
                                        <a href="{{ route('activity.user', $activity->user_id) }}"
                                           data-toggle="tooltip" title="@lang('View Activity Log')">
                                            {{ $activity->user->present()->nameOrEmail }}
                                        </a>
                                    @endif
                                </td>
                            @endif
                            <td>{{ $activity->ip_address }}</td>
                            <td>{{ __($activity->description, $activity->additional_data ?? []) }}</td>
                            <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                            <td class="text-center">
                                <a tabindex="0" role="button" class="btn btn-icon"
                                   data-trigger="focus"
                                   data-placement="left"
                                   data-toggle="popover"
                                   title="@lang('User Agent')"
                                   data-content="{{ $activity->user_agent }}">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>

    {!! $activities->render() !!}
@stop

@section('scripts')
    <script src="{{ url("vendor/plugins/activity-log/js/activity-log.js") }}"></script>
@stop