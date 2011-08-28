/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

$.facebox.settings.loadingImage = '/js/facebox/src/loading.gif';
$.facebox.settings.faceboxHtml = '\
<div id="facebox"> \
    <div class="popup"> \
        <div class="body"> \
            <div class="content"> \
            </div> \
        </div> \
    </div> \
</div>';
$(document).bind('loading.facebox', function() {
    $('#facebox .loading').css({
        "background-color" : "white",
        "width"  : "300px",
        "height" : "300px",
    });
    $('#facebox .loading img').css({
        "margin-top" : "118px",
    });
});

$(function() {
    $(".plotable").sortable({
        "opacity" : 0.5,
        "cursor" : "move",
        "containment" : "#plot_page_gadget",
        "items" : ".plot_item",
        "connectWith" : ".plotable",
        "placeholder" : "plot_item ui-state-highlight",
        "update": function(e, ui) {
            $(".plotable").each(function() {
                $("#gadget_position_"+$(this).attr("id")).val($(this).sortable("toArray"));
            });
        },
        "beforeStop" : function(e, ui) {
            if ("gadget_item ui-draggable" == ui.item.attr("class")) {  // TODO: to exact
                ui.item.attr("class", "plot_item new_plot_item").children().hide();
                ui.item.find("dt a").first().click();
            }
        }
    });
});

$(function() {
    $(".gadget_item").draggable({
        cursor: "move",
        connectToSortable: "#plotArea .plotable",
        revert: "invalid",
        opacity: 0.5,
        helper: function (e) {
            return $('<div class="plot_item" style="width: 250px; height: 1em; text-align: center"><p>' + $(this).find("dt").first().text() + '</p></div>');
        }
    });
});

function addPageGadget(url)
{
    jQuery.facebox({ ajax: url });
}

function editPageGadget(url)
{
    jQuery.facebox({ ajax: url });
}

function filterPageGadget()
{
    $('.gadget_list dl').css('display', 'inline-block').filter(function (index) {
        var patterns = $('#filter_gadget').val().split(' ');
        for (var i = 0; i < patterns.length; i++) {
            var re = new RegExp(patterns[i], 'i');
            var result = ($(this).text().search(re) == -1);
            if (result) {
                return true;
            }
        }

        return false;
    }).css('display', 'none');
}

function getFaceBoxFormValue(elm)
{
    var result = '';

    elm.find("form * td *").each(function(index) {
        if ("societo_page_page_gadget_point" != $(this).attr("id") && "societo_page_page_gadget_sortOrder" != $(this).attr("id")) {  // TODO: to excat
            result += index + ':' + $(this).val() + '####';
        }
    });

    return result;
}

// append close.facebox canceler
var events = $._data(document, "events");
var handlers = [];
for (key in events) {
    for (var i = 0; i < events[key].length; i++) {
        if ("facebox.close" == events[key][i].namespace + "." + events[key][i].type) {
                handlers.push(events[key][i].handler);
        }
    }
}
handlers.unshift(function(e) {
    if (getFaceBoxFormValue($(this)) != $(this).data("societo.originalInput")) {
        if (!window.confirm('The changes you made will be lost if you close this box. Do you continue?')) {
            e.stopImmediatePropagation();

            return false;
        }
    }

    $(this).data("societo.originalInput", "");

    return false;
});

$(document).unbind('close.facebox');

for (var i = 0; i < handlers.length; i++) {
    $(document).bind('close.facebox', handlers[i]);
}

$(document).bind('close.facebox', function(e) {
    $('.new_plot_item').remove();
});

$(document).bind('afterReveal.facebox', function(e) {
    $(this).data("societo.originalInput", getFaceBoxFormValue($(this)));

    if (!$(".new_plot_item").length) {
        return null;
    }

    var newPlotPoint = $(".new_plot_item").parent().attr("id");
    if (newPlotPoint) {
        $("#societo_page_page_gadget_point").val(newPlotPoint);
    }

    var newSortOrder = 0;
    if ($(".new_plot_item").next().length) {
        newSortOrder = $(".new_plot_item").next().attr("class").match(/order_([0-9]+)/)[1];
    } else if ($(".new_plot_item").prev().length) {
        newSortOrder = $(".new_plot_item").prev().attr("class").match(/order_([0-9]+)/)[1] + 1;
    }

    $("#societo_page_pagegadget_sortOrder").val(newSortOrder);
});

function filterByFlavour(flavour)
{
    $("#plotArea .plotable, #plotArea .plot_item").each(function(){
        var target = $(this).data("flavour");
        var found = false;
        if (target instanceof Array) {
            found = (-1 != jQuery.inArray(flavour, target));
        } else {
            found = (-1 != target.indexOf(flavour));
        }

        if (found) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

$("#target_device").change(function (e) {
    filterByFlavour($("#target_device option:selected").val());
});

$(document).ready(function (e) {
    filterByFlavour($("#target_device option:selected").val());
});
