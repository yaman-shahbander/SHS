<div class='btn-group btn-group-sm'>
  @can('categories.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('country.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('balance.add')
  <a data-toggle="tooltip" data-placement="bottom" title="Add Balance" href="{{ route('balance.add', $id) }}" class='btn btn-link'>
    <i class="fa fa-plus"></i>
  </a>
  @endcan

  @can('balance.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="Edit Balance" href="{{ route('balance.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan


@can('balance.destroy')
{!! Form::open(['route' => ['balance.destroy', $id], 'method' => 'delete']) !!}
{!! Form::button('<i class="fa fa-trash"></i>', [
'type' => 'submit',
'class' => 'btn btn-link text-danger',
'onclick' => "return confirm('Are you sure?')"
]) !!}
{!! Form::close() !!}
@endcan

</div>
