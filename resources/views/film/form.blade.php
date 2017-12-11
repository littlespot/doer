{{ csrf_field() }}
<input type="hidden" name="id" value="{{$film->id}}">
<input type="hidden" name="completed" value="{{$film->completed}}">
<input type="hidden" name="step" value="{{$step}}">