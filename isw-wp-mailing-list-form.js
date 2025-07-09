jQuery(document).ready(function ($) {
    $('#btn_bg_color').wpColorPicker();
    $('#btn_text_color').wpColorPicker();
    $('#input_text_color').wpColorPicker();
    $('#input_border_color').wpColorPicker();
    $('#btn_border_color').wpColorPicker();
    $('#input_outline_color').wpColorPicker();
    $('#isw_box_shadow_color').wpColorPicker({
        change: function (event, ui) {
            $('#isw_box_shadow_color').val(ui.color.toString());
            updateBoxShadowInput();
        }
    });

    // Funkcija za ažuriranje box-shadow preview i hidden inputa
    function updateBoxShadowInput() {
        var inset = $('#isw_box_shadow_inset').is(':checked') ? 'inset ' : '';
        var h = $('#isw_h_offset').val();
        var v = $('#isw_v_offset').val();
        var b = $('#isw_blur').val();
        var s = $('#isw_spread').val();
        var c = $('#isw_box_shadow_color').val();
        var val = inset + h + 'px ' + v + 'px ' + b + 'px ' + s + 'px ' + c;
        $('#btn_box_shadow').val(val);
        $('#isw_box_shadow_preview').css('box-shadow', val);
    }

    // Sinhronizacija range i number inputa
    $('#isw_h_offset').on('input', function () {
        $('#isw_h_offset_num').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_h_offset_num').on('input', function () {
        $('#isw_h_offset').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_v_offset').on('input', function () {
        $('#isw_v_offset_num').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_v_offset_num').on('input', function () {
        $('#isw_v_offset').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_blur').on('input', function () {
        $('#isw_blur_num').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_blur_num').on('input', function () {
        $('#isw_blur').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_spread').on('input', function () {
        $('#isw_spread_num').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_spread_num').on('input', function () {
        $('#isw_spread').val(this.value);
        updateBoxShadowInput();
    });
    $('#isw_box_shadow_color').on('input change', updateBoxShadowInput);
    $('#isw_box_shadow_inset').on('change', updateBoxShadowInput);

    // Pozovi odmah na učitavanje stranice da bi svi inputi i preview bili ažurirani
    if ($('#isw_box_shadow_color').length) {
        updateBoxShadowInput();
    }

});
