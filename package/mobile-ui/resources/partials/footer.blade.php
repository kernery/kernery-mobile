{!! MobileUi::asset()->container('before_footer')->styles() !!}
{!! MobileUi::asset()->styles() !!}
{!! MobileUi::asset()->container('footer')->styles() !!}
{!! MobileUi::asset()->container('footer')->scripts() !!}
{!! MobileUi::asset()->container('after_footer')->styles() !!}

{!! apply_filters(MOBILE_UI_FRONT_FOOTER, null) !!}
