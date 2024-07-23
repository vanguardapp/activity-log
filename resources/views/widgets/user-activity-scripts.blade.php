<script>
    var labels = @json(array_keys($activities));
    var activities = @json(array_values($activities));
    var trans = {
        chartLabel: "{{ __('Registration History')  }}",
        action: "{{ __('action')  }}",
        actions: "{{ __('actions')  }}"
    };
</script>
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script src="{{ asset('assets/js/as/dashboard-default.js') }}"></script>
