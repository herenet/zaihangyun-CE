<div class="user-panel">
    <select class="form-control" name="{{$custom_data['app_select']['name']}}" >
        @foreach($custom_data['app_select']['options'] as $select => $option)
            <option value="{{$select}}">{{$option}}</option>
        @endforeach
    </select>
</div>
