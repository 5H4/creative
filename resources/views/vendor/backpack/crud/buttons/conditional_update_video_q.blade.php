@if (is_null($entry->process_time_start))
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}" class="btn btn-sm btn-link">
        <i class="la la-edit"></i> Update
    </a>
@endif

<!--delete button-->
@if (is_null($entry->process_time_start))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/delete') }}" class="btn btn-sm btn-link">
    <i class="la la-trash"></i> Delete
</a>
@endif

