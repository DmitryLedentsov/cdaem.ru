﻿/*
*       Developed by Justin Mead
*       ©2011 MeadMiracle
*		www.meadmiracle.com / meadmiracle@gmail.com
*       Version 1.3
*       Testing: IE8/Windows XP
*                Firefox/Windows XP
*                Chrome/Windows XP
*       Licensed under the Creative Commons GPL http://creativecommons.org/licenses/GPL/2.0/
*/

(function ($) {
    var settings = new Array();
    var group1 = new Array();
    var group2 = new Array();
    var onSort = new Array();
    $.configureBoxes = function (options) {
        var index = settings.push({
            box1View: 'box1View',
            box1Storage: 'box1Storage',
            box1Filter: 'box1Filter',
            box1Clear: 'box1Clear',
            box1Counter: 'box1Counter',
            box2View: 'box2View',
            box2Storage: 'box2Storage',
            box2Filter: 'box2Filter',
            box2Clear: 'box2Clear',
            box2Counter: 'box2Counter',
            to1: 'to1',
            allTo1: 'allTo1',
            to2: 'to2',
            allTo2: 'allTo2',
            transferMode: 'move',
            sortBy: 'text',
            useFilters: true,
            useCounters: true,
            useSorting: true,
            selectOnSubmit: true
        });
        index--;
        $.extend(settings[index], options);
        group1.push({
            view: settings[index].box1View,
            storage: settings[index].box1Storage,
            filter: settings[index].box1Filter,
            clear: settings[index].box1Clear,
            counter: settings[index].box1Counter,
            index: index
        });
        group2.push({
            view: settings[index].box2View,
            storage: settings[index].box2Storage,
            filter: settings[index].box2Filter,
            clear: settings[index].box2Clear,
            counter: settings[index].box2Counter,
            index: index
        });
        if (settings[index].sortBy == 'text') {
            onSort.push(function (a, b) {
                var aVal = a.text.toLowerCase();
                var bVal = b.text.toLowerCase();
                if (aVal < bVal) {
                    return -1;
                }
                if (aVal > bVal) {
                    return 1;
                }
                return 0;
            });
        } else {
            onSort.push(function (a, b) {
                var aVal = a.value.toLowerCase();
                var bVal = b.value.toLowerCase();
                if (aVal < bVal) {
                    return -1;
                }
                if (aVal > bVal) {
                    return 1;
                }
                return 0;
            });
        }
        if (settings[index].useFilters) {
            $('#' + group1[index].filter).keyup(function () {
                Filter(group1[index]);
            });
            $('#' + group2[index].filter).keyup(function () {
                Filter(group2[index]);
            });
            $('#' + group1[index].clear).click(function () {
                ClearFilter(group1[index]);
            });
            $('#' + group2[index].clear).click(function () {
                ClearFilter(group2[index]);
            });
        }
        if (IsMoveMode(settings[index])) {
            $('#' + group2[index].view).dblclick(function () {
                MoveSelected(group2[index], group1[index]);
            });
            $('#' + settings[index].to1).click(function () {
                MoveSelected(group2[index], group1[index]);
            });
            $('#' + settings[index].allTo1).click(function () {
                MoveAll(group2[index], group1[index]);
            });
        } else {
            $('#' + group2[index].view).dblclick(function () {
                RemoveSelected(group2[index], group1[index]);
            });
            $('#' + settings[index].to1).click(function () {
                RemoveSelected(group2[index], group1[index]);
            });
            $('#' + settings[index].allTo1).click(function () {
                RemoveAll(group2[index], group1[index]);
            });
        }
        $('#' + group1[index].view).dblclick(function () {
            MoveSelected(group1[index], group2[index]);
        });
        $('#' + settings[index].to2).click(function () {
            MoveSelected(group1[index], group2[index]);
        });
        $('#' + settings[index].allTo2).click(function () {
            MoveAll(group1[index], group2[index]);
        });
        if (settings[index].useCounters) {
            UpdateLabel(group1[index]);
            UpdateLabel(group2[index]);
        }
        if (settings[index].useSorting) {
            SortOptions(group1[index]);
            SortOptions(group2[index]);
        }
        $('#' + group1[index].storage + ',#' + group2[index].storage).css('display', 'none');
        if (settings[index].selectOnSubmit) {
            $('#' + settings[index].box2View).closest('form').submit(function () {
                $('#' + settings[index].box2View).children('option').attr('selected', 'selected');
            });
        }
    };

    function UpdateLabel(group) {
        var showingCount = $("#" + group.view + " option").length;
        var hiddenCount = $("#" + group.storage + " option").length;
        $("#" + group.counter).text('Showing ' + showingCount + ' of ' + (showingCount + hiddenCount));
    }

    function Filter(group) {
        var index = group.index;
        var filterLower;
        if (settings[index].useFilters) {
            filterLower = $('#' + group.filter).val().toString().toLowerCase();
        } else {
            filterLower = '';
        }
        $('#' + group.view + ' option').filter(function (i) {
            var toMatch = $(this).text().toString().toLowerCase();
            return toMatch.indexOf(filterLower) == -1;
        }).appendTo('#' + group.storage);
        $('#' + group.storage + ' option').filter(function (i) {
            var toMatch = $(this).text().toString().toLowerCase();
            return toMatch.indexOf(filterLower) != -1;
        }).appendTo('#' + group.view);
        try {
            $('#' + group.view + ' option').removeAttr('selected');
        } catch (ex) {
        }
        if (settings[index].useSorting) {
            SortOptions(group);
        }
        if (settings[index].useCounters) {
            UpdateLabel(group);
        }
    }

    function SortOptions(group) {
        var $toSortOptions = $('#' + group.view + ' option');
        $toSortOptions.sort(onSort[group.index]);
        $('#' + group.view).empty().append($toSortOptions);
    }

    function MoveSelected(fromGroup, toGroup) {
        if (IsMoveMode(settings[fromGroup.index])) {
            $('#' + fromGroup.view + ' option:selected').appendTo('#' + toGroup.view);
        } else {
            $('#' + fromGroup.view + ' option:selected:not([class*=copiedOption])').clone().appendTo('#' + toGroup.view).end().end().addClass('copiedOption');
        }
        try {
            $('#' + fromGroup.view + ' option,#' + toGroup.view + ' option').removeAttr('selected');
        } catch (ex) {
        }
        Filter(toGroup);
        if (settings[fromGroup.index].useCounters) {
            UpdateLabel(fromGroup);
        }
    }

    function MoveAll(fromGroup, toGroup) {
        if (IsMoveMode(settings[fromGroup.index])) {
            $('#' + fromGroup.view + ' option').appendTo('#' + toGroup.view);
        } else {
            $('#' + fromGroup.view + ' option:not([class*=copiedOption])').clone().appendTo('#' + toGroup.view).end().end().addClass('copiedOption');
        }
        try {
            $('#' + fromGroup.view + ' option,#' + toGroup.view + ' option').removeAttr('selected');
        } catch (ex) {
        }
        Filter(toGroup);
        if (settings[fromGroup.index].useCounters) {
            UpdateLabel(fromGroup);
        }
    }

    function RemoveSelected(removeGroup, otherGroup) {
        $('#' + otherGroup.view + ' option.copiedOption').add('#' + otherGroup.storage + ' option.copiedOption').remove();
        try {
            $('#' + removeGroup.view + ' option:selected').appendTo('#' + otherGroup.view).removeAttr('selected');
        } catch (ex) {
        }
        $('#' + removeGroup.view + ' option').add('#' + removeGroup.storage + ' option').clone().addClass('copiedOption').appendTo('#' + otherGroup.view);
        Filter(otherGroup);
        if (settings[removeGroup.index].useCounters) {
            UpdateLabel(removeGroup);
        }
    }

    function RemoveAll(removeGroup, otherGroup) {
        $('#' + otherGroup.view + ' option.copiedOption').add('#' + otherGroup.storage + ' option.copiedOption').remove();
        try {
            $('#' + removeGroup.storage + ' option').clone().addClass('copiedOption').add('#' + removeGroup.view + ' option').appendTo('#' + otherGroup.view).removeAttr('selected');
        } catch (ex) {
        }
        Filter(otherGroup);
        if (settings[removeGroup.index].useCounters) {
            UpdateLabel(removeGroup);
        }
    }

    function ClearFilter(group) {
        $('#' + group.filter).val('');
        $('#' + group.storage + ' option').appendTo('#' + group.view);
        try {
            $('#' + group.view + ' option').removeAttr('selected');
        } catch (ex) {
        }
        if (settings[group.index].useSorting) {
            SortOptions(group);
        }
        if (settings[group.index].useCounters) {
            UpdateLabel(group);
        }
    }

    function IsMoveMode(currSettings) {
        return currSettings.transferMode == 'move';
    }
})(jQuery);