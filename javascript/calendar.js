
(function($) {
    $.fn.calendar = function(options) {

        var settings = $.extend({
            table : $(this),
            date : null,
            view : 'month',
            post : null,
            controller : null
        }, options);

        var next;
        var prev;
        var calendar;

        var action = {
            drawMonthView:function(date = settings.date) {
                $.post(settings.controller, {date : date}, function(response) {
                    if ($.trim(response)) {
                        calendar = JSON.parse(response);
                        prev = calendar.last_month;
                        next = calendar.next_month;

                        action.reset(calendar.month + ' ' + calendar.year);
                        settings.table.prepend('<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>');
                        settings.table.attr('data-view', 'month');
                        settings.date = calendar.month + ' ' + calendar.year;

                        var week = '';
                        var lm_end = parseInt(calendar.lm_day_count);
                        var lm_start = lm_end - calendar.dow + 1;

                        for (var day = lm_start; day <= lm_end; day++) {
                            week = week + '<td class="om">' + day + '</td>';
                        }

                        for (var day = 1; day <= calendar.day_count; day++, calendar.dow++) {
                            var date = day + ' ' + calendar.month + ' ' + calendar.year;
                            
                            if (calendar.todays_date == date) {
                                week = week + '<td data-date="' + day + ' ' + calendar.month + ' ' + calendar.year + '" class="today cm"><button type="button" class="btn btn-cal-day">' + day + '</button><div class="events">';
                            } else {
                                week = week + '<td data-date="' + day + ' ' + calendar.month + ' ' + calendar.year + '" class="cm"><button type="button" class="btn btn-cal-day">' + day + '</button><div class="events">';
                            }
                            week = week + '</div></td>';
                            
                            if (calendar.dow % 7 == 6 || day == calendar.day_count) {
                                if (day == calendar.day_count) {
                                    var cells = 6 - (calendar.dow % 7);
                                    for (var num = 1; num <= cells; num++) {
                                        week = week + '<td class="nm">' + num + '</td>';
                                    }
                                }
                                settings.table.append('<tr class="calendar-week">' + week + '</tr>');
                                week = '';
                            }
                        }
                    }
                }).always(function() {
                    if (!date) {
                        date = calendar.todays_date;
                    }
                    action.getCalData(date);
                });
            },
            drawWeekView:function(date = null) {
                $.post(settings.controller, {date : date}, function(response) {
                    if ($.trim(response)) {
                        calendar = JSON.parse(response);
                        var week = [];
                        var lm_end = parseInt(calendar.lm_day_count);
                        var lm_start = lm_end - calendar.dow + 1;
                        var tm = calendar.month_year;
                        var lm = calendar.last_month;
                        var nm = calendar.next_month;

                        settings.table.attr('data-view', 'week');

                        for (var day = lm_start; day <= lm_end; day++) {
                            week.push(day + ' ' + lm);
                        }

                        for (var day = 1; day <= calendar.day_count; day++, calendar.dow++) {
                            week.push(day + ' ' + tm);
                            if (calendar.dow % 7 == 6 || day == calendar.day_count) {
                                if (day == calendar.day_count) {
                                    var cells = 6 - (calendar.dow % 7);
                                    for (var num = 1; num <= cells; num++) {
                                        week.push(num + ' ' + nm);
                                    }
                                }
                            }
                        }

                        var weeks = [];
                        var w1 = week.slice(0, 7), w2 = week.slice(7, 14), w3 = week.slice(14, 21), w4 = week.slice(21, 28), w5 = week.slice(28, 35), w6 = week.slice(35, 42);
                        var week_start;
                        var week_end;
                        var w;

                        weeks.push(w1, w2, w3, w4, w5, w6);

                        $.each(weeks, function(k, v) {
                            if (!w) {
                                if ($.inArray(calendar.day + ' ' + tm, v) > -1) {
                                    w = v;
                                }
                            }
                        });

                        var lw = parseInt(w[0].split(' ')[0]);
                        var lwm = w[0].split(' ')[1] + ' ' + w[0].split(' ')[2];
                        var nw = parseInt(w[6].split(' ')[0]);
                        var nwm = w[6].split(' ')[1] + ' ' + w[6].split(' ')[2];

                        prev = lw - 1 + ' ' + tm;

                        if (lw == 1) {
                            prev = calendar.lm_day_count + ' ' + calendar.last_month;
                        }
                        if (lwm == lm) {
                            prev = lw - 1 + ' ' + calendar.last_month;
                        }

                        next = nw + 1 + ' ' + tm;

                        if (nw == 31) {
                            next = 1 + ' ' + calendar.next_month;
                        }
                        if (nwm == nm) {
                            next = parseInt(w[6].split(' ')[0]) + 1 + ' ' + calendar.next_month;
                        }

                        week_start = w[0];
                        week_end = w[6];

                        if (week_start.split(' ')[1] == week_end.split(' ')[1]) {
                            week_start = week_start.split(' ')[0];
                        } else {
                            week_start = week_start.split(' ')[0] + ' ' + week_start.split(' ')[1];
                        }

                        action.reset(week_start + ' - ' + week_end);
                        settings.table.append('<tr><th></th><th>Sun ' + w[0].split(' ')[0] + '</th><th>Mon ' + w[1].split(' ')[0] + '</th><th>Tue ' + w[2].split(' ')[0] + '</th><th>Wed ' + w[3].split(' ')[0] + '</th><th>Thu ' + w[4].split(' ')[0] + '</th><th>Fri ' + w[5].split(' ')[0] + '</th><th>Sat ' + w[6].split(' ')[0] + '</th></tr>');

                        var time = 0;
                        var row = '';
            
                        for (var hour = 0; hour <= 11; hour++, time++) {
                            row = '<tr><td class="time-td">' + hour + 'am</td>';
                            $.each(w, function(k, v) {
                                row = row + '<td data-day="' + v + '" data-time="' + time + '"></td>';
                            });
                            row = row + '</tr>';
                            settings.table.append(row);
                            row = '';
                        }

                        time = 12;

                        for (var hour = 0; hour <= 11; hour++, time++) {
                            row = '<tr><td class="time-td">' + hour + 'pm</td>';
                            $.each(w, function(k, v) {
                                row = row + '<td data-day="' + v + '" data-time="' + time + '"></td>';
                            });
                            row = row + '</tr>';
                            settings.table.append(row);
                            row = '';
                        }

                        $('td:first-child').each(function() {
                            if ($(this).text() == '0am') {
                                $(this).text('12am')
                            }
                            if ($(this).text() == '0pm') {
                                $(this).text('12pm')
                            }
                        });
                    }
                }).always(function() {
                    if (!date) {
                        date = calendar.todays_date;
                    }
                    action.getCalData(date);
                });
            },
            drawDayView:function(date = null) {
                $.post(settings.controller, {date : date}, function(response) {
                    if ($.trim(response)) {
                        calendar = JSON.parse(response);
                        var yesterday = parseInt(calendar.day) - 1;
                        var tomorrow = parseInt(calendar.day) + 1;
                        var my = calendar.month + ' ' + calendar.year;
                        var lm = calendar.last_month;
                        var nm = calendar.next_month;
                        var day_count = parseInt(calendar.day_count);

                        prev = yesterday + ' ' + my;
                        next = tomorrow + ' ' + my;

                        if (yesterday == 0) {
                            yesterday = calendar.lm_day_count;
                            prev = yesterday + ' ' + lm;
                        }

                        if (tomorrow > day_count) {
                            tomorrow = 1;
                            next = tomorrow + ' ' + nm;
                        }
                        
                        action.reset(calendar.day + ' ' + calendar.month + ' ' + calendar.year);

                        $('.calendar').attr('id', 'day-view');
                        settings.table.attr('data-view', 'day');
                        settings.table.prepend('<th></th><th colspan="8">' + calendar.today + '</th>');
                        settings.table.append('<tr><td class="time-td">12am</td><td data-time="0"></td></tr>');
            
                        var time = 1;
            
                        for (var hour = 1; hour <= 11; hour++, time++) {
                            col = '<td class="time-td">' + hour + 'am' + '</td><td data-time="' + time + '"></td>';
                            settings.table.append('<tr>' + col + '</tr>');
                        }
            
                        settings.table.append('<tr><td class="time-td">12pm</td><td data-time="12"></td></tr>');

                        time = 13;
            
                        for (var hour = 1; hour <= 11; hour++, time++) {
                            col = '<td class="time-td">' + hour + 'pm' + '</td><td data-time="' + time + '"></td>';
                            settings.table.append('<tr>' + col + '</tr>');
                        }
                    }
                }).always(function() {
                    if (!date) {
                        date = calendar.todays_date;
                    }
                    action.getCalData(date);
                });
            },
            reset:function(title) {
                $('.this-month').text(title);
                $('.calendar').removeAttr('id');
                $('th').remove();
                $('tr').remove();
                $('.week').remove();
                $('.calendar-week').remove();
            },
            navigate:function() {
                $('body').on('click', '.btn-prev', function() {
                    if ($('table').attr('data-view') == 'day') {
                        action.drawDayView(prev);   
                    }
                    if ($('table').attr('data-view') == 'week') {
                        action.drawWeekView(prev);
                    }
                    if ($('table').attr('data-view') == 'month') {
                        action.drawMonthView(prev);  
                    } 
                });
            
                $('body').on('click', '.btn-next', function() {
                    if ($('table').attr('data-view') == 'day') {
                        action.drawDayView(next);   
                    }
                    if ($('table').attr('data-view') == 'week') {
                        action.drawWeekView(next);
                    }
                    if ($('table').attr('data-view') == 'month') {
                        action.drawMonthView(next);  
                    } 
                });

                $('body').on('click', '.btn-today', function() {
                    if ($('table').attr('data-view') == 'day') {
                        action.drawDayView(null);   
                    }
                    if ($('table').attr('data-view') == 'week') {
                        action.drawWeekView(null);   
                    }
                    if ($('table').attr('data-view') == 'month') {
                        action.drawMonthView(null);  
                    }  
                });

                $('body').on('click', '.btn-month', function() {
                    if ($('table').attr('data-view') != 'month') {
                        action.drawMonthView(null);  
                    }
                });
            
                $('body').on('click', '.btn-week', function() {
                    if ($('table').attr('data-view') != 'week') {
                        action.drawWeekView();
                    }   
                });
            
                $('body').on('click', '.btn-day', function() {
                    if ($('table').attr('data-view') != 'day') {
                        action.drawDayView();   
                    }
                });

                $('body').on('click', '.btn-cal-day, th', function() {
                    var day = ($(this).text());
                    action.drawDayView(day + ' ' + settings.date);      
                });
            },
            events:function() {
                $(document).on('click', '.event', function() {
                    $('.event-large').remove();
                    $('.event').removeAttr('style');
                    
                    var text = $(this).text().trim().split(/ (.+)/)[1];
                    var id = $(this).attr('data-id');
                    var timespan = $('span', this).text();
                    var bottom = 160;
                    var left = 50;

                    var event = `<div class="event-large">
                                    <div class="event-large-header">
                                    <i class="fas fa-calendar-alt fa-fw"></i> Requests 
                                    <button type="button" class="btn-close-event"><i class="fas fa-times fa-fw"></i> Close</button>
                                    <a href="/requests/request/` + id + `"><i class="fas fa-pencil-alt fa-fw"></i> Edit</a>
                                    </div>
                                    <p>` + text.replace(timespan, '') + `<span><i class="far fa-clock fa-fw"></i> ` + timespan + `</span></p><div class="arrow"></div>
                                </div>`;
                    
                    $('.event-large', this).text('');
                    $(this).css({'color' : '#fff', 'background-color' : '#659dac'});
                    $(this).append(event);     
                    $('.event-large').css({'bottom' : bottom + '%', 'left' : left + '%'});
                });
            
                $('body').on('click', '.btn-close-event', function() {
                    $('.event-large').remove();
                    $('.event').removeAttr('style');
                });
            },
            getCalData:function(date) {
                $.post(settings.post, {date : date}, function(response) {
                    if ($.trim(response)) {
                        var json = JSON.parse(response);
                        var data = json.data;
                        var this_cell;
                        var start_day;
                        var end_day;
                        var start_month;
                        var time;
                        var start_time;
                        var end_time;
                        var view = $('table').attr('data-view');
                        var event;
                        var week_day;
                        var today;
                        
                        if (view == 'month') {
                            $('td.cm').each(function() {
                                this_cell = $(this);
                                this_day = parseInt(this_cell.text());

                                $(data).each(function(k, v) {
                                    start_day = parseInt(v.start_day);
                                    end_day = parseInt(v.end_day);
                                    start_month = Date.parse(v.start_month_iso);
                                    last_month = Date.parse(v.last_month_iso);
                                    event = `<div class="event" data-id="` + v.id + `">
                                                <p>` + v.start_time_short + ` ` + v.message + ` <span>` + v.start_day + `/` + v.start_month.replace('-', '/') + ' ' + v.start_time + ` - ` + v.end_time + `</span></p>
                                            </div>`;

                                    if (start_month == last_month) {
                                        start_day = 1;
                                    }

                                    if (this_day >= start_day && this_day <= end_day) {
                                        this_cell.find('.events').append(event);
                                    }
                                });
   
                                sizeEvents($(this));
                            });
                        }

                        if (view == 'week') {
                            $('td').each(function() {
                                if (!$(this).hasClass('time-td')) {

                                    this_cell = $(this);
                                    time = this_cell.attr('data-time');

                                    var wd_y = this_cell.attr('data-day').split(' ')[2];
                                    var wd_m = this_cell.attr('data-day').split(' ')[1];
                                    var wd_d = this_cell.attr('data-day').split(' ')[0];

                                    wd_m = new Date(Date.parse(wd_m + ' ' + wd_d + ', ' + wd_y)).getMonth()+1;
                                    week_day = Date.parse(wd_y + '-' + wd_m + '-' + wd_d);

                                    $(data).each(function(k, v) {
                                        start_month = Date.parse(v.start_month_iso);
                                        last_month = Date.parse(v.last_month_iso);
                                        start_day = Date.parse(v.start_month_iso + '-' + v.start_day);
                                        end_day = Date.parse(v.end_month_iso + '-' + v.end_day);
                                        time = parseInt(time);
                                        start_time = parseInt(v.start_time_mil);
                                        end_time = parseInt(v.end_time_mil);
                                        
                                        event = `<div class="event" data-id="` + v.id + `">
                                                    <p>` + v.start_time_short + ` ` + v.message + ` <span>` + v.start_day + `/` + v.start_month.replace('-', '/') + ' ' + v.start_time + ` - ` + v.end_time + `</span></p>
                                                </div>`;
                                        
                                        if (start_month == last_month) {
                                            start_day = Date.parse(v.start_month_iso + '-' + 1);
                                        }
  
                                        if (week_day >= start_day && week_day <= end_day) {
                                            if (time >= start_time && time <= end_time) {
                                                this_cell.append(event);
                                            }
                                        }
                                    });

                                    sizeEvents($(this));
                                }
                            });
                        }

                        if (view == 'day') {
                            $('td').each(function() {
                                if (!$(this).hasClass('time-td')) {
                                    this_cell = $(this);
                                    time = parseInt(this_cell.attr('data-time'));
                                    
                                    $(data).each(function(k, v) {
                                        start_day = parseInt(v.start_day);
                                        end_day = parseInt(v.end_day);
                                        time = parseInt(time);
                                        start_time = parseInt(v.start_time_mil);
                                        end_time = parseInt(v.end_time_mil);
                                        start_month = Date.parse(v.start_month);
                                        last_month = Date.parse(v.last_month);
                                        today = parseInt(v.today);

                                        event = `<div class="event" data-id="` + v.id + `">
                                                    <p>` + v.start_time_short + ` ` + v.message + ` <span>` + v.start_day + `/` + v.start_month.replace('-', '/') + ' ' + v.start_time + ` - ` + v.end_time + `</span></p>
                                                </div>`;

                                        if (start_month == last_month) {
                                            start_day = 1;
                                        }

                                        if (today >= start_day && today <= end_day || today == start_day) {
                                            if (time >= start_time && time <= end_time) {
                                                this_cell.append(event);
                                            }
                                        }
                                    });
                                }
                                
                                sizeEvents($(this));
                            });
                        }
                    }
                });
            }
        }

        return this.each(function() {
            switch (settings.view) {
                case 'day': action.drawDayView(); break;
                case 'week': action.drawWeekView(); break;
                default: action.drawMonthView(); break;
            }

            action.navigate();
            action.events();
        });
    }
}(jQuery));

function sizeEvents(obj) {
    var events = obj.find('.event').length;
    var size = '';
    $('.event').each(function() {
        switch (events) {
            case 2: size = 'col dual'; break;
            case 3: size = 'col triple'; break;
            case 4: size = 'col quad'; break;
            case 5: size = 'col penta'; break;
            case 6: size = 'col hexa'; break;
            case 7: size = 'col hepta'; break;
            case 8: size = 'col octa'; break;
            case 9: size = 'col nona'; break;
            case 10: size = 'col deca'; break;
        }
        if (events > 10) {
            size = 'col deca';
        }
    });
    obj.find('.event').addClass(size);
}