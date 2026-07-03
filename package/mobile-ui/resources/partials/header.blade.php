{!! apply_filters(MOBILE_UI_FRONT_META, null) !!}

{!! MobileUi::asset()->container('before_header')->styles() !!}
{!! MobileUi::asset()->styles() !!}
{!! MobileUi::asset()->container('header')->styles() !!}
{!! MobileUi::asset()->container('header')->scripts() !!}
{!! MobileUi::asset()->container('after_header')->styles() !!}

{!! apply_filters(MOBILE_UI_FRONT_HEADER, null) !!}

