jQuery(document).ready(function ($) {
    $('.isw-ml-color-field').not('#isw_box_shadow_color').wpColorPicker();
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

    function bindPaddingGroup(prefix) {
        var sameAll = $('#' + prefix + '_same_all');
        var top = $('#' + prefix + '_top');
        var others = $('#' + prefix + '_right, #' + prefix + '_bottom, #' + prefix + '_left');

        if (!sameAll.length || !top.length || !others.length) {
            return;
        }

        function syncGroup() {
            if (sameAll.is(':checked')) {
                others.val(top.val()).prop('readonly', true);
            } else {
                others.prop('readonly', false);
            }
        }

        sameAll.on('change', syncGroup);
        top.on('input', function () {
            if (sameAll.is(':checked')) {
                others.val(this.value);
            }
        });

        syncGroup();
    }

    bindPaddingGroup('input_padding');
    bindPaddingGroup('button_padding');

    var $tabs = $('.isw-ml-admin__tabs .nav-tab');
    var $panels = $('.isw-ml-tab-panel');

    function activateTab(panelId) {
        $tabs.each(function () {
            var $tab = $(this);
            var isActive = $tab.data('panel') === panelId;

            $tab.toggleClass('nav-tab-active', isActive);
            $tab.attr('aria-selected', isActive ? 'true' : 'false');
        });

        $panels.each(function () {
            var $panel = $(this);
            var isActive = $panel.attr('id') === panelId;

            $panel.toggleClass('isw-ml-tab-panel-active', isActive);
            $panel.prop('hidden', !isActive);
        });
    }

    $tabs.on('click', function () {
        activateTab($(this).data('panel'));
    });

    if ($tabs.length && $panels.length) {
        activateTab($('.isw-ml-admin__tabs .nav-tab-active').data('panel') || $tabs.first().data('panel'));
    }

});
