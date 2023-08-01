<div>
    <Label for="{{ $id }}" >{{ $label }}</Label>
    <input type="radio" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}">
    {{ $slot }}
</div>