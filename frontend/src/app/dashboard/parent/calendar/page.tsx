'use client';

import { useState, useEffect } from 'react';
import { HiCalendar, HiLocationMarker, HiClock } from 'react-icons/hi';
import { eventsApi } from '@/lib/api';
import { format, parseISO } from 'date-fns';
import LoadingSpinner from '@/components/ui/LoadingSpinner';
import { Event } from '@/types';

export default function ParentCalendarPage() {
  const [loading, setLoading] = useState(true);
  const [events, setEvents] = useState<Event[]>([]);
  const [selectedMonth, setSelectedMonth] = useState(format(new Date(), 'yyyy-MM'));

  useEffect(() => {
    loadEvents();
  }, [selectedMonth]);

  const loadEvents = async () => {
    setLoading(true);
    try {
      const res = await eventsApi.getAll({ month: selectedMonth });
      setEvents(res.data.data || []);
    } catch {
    } finally {
      setLoading(false);
    }
  };

  const getMonthDays = () => {
    const date = parseISO(`${selectedMonth}-01`);
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const days = [];
    for (let d = 1; d <= lastDay.getDate(); d++) {
      days.push(new Date(year, month, d));
    }
    return days;
  };

  const getEventsForDay = (day: Date) => {
    const dateStr = format(day, 'yyyy-MM-dd');
    return events.filter((e) => format(parseISO(e.eventDate), 'yyyy-MM-dd') === dateStr);
  };

  if (loading) return <LoadingSpinner size="lg" className="min-h-[60vh]" />;

  const days = getMonthDays();
  const startDay = new Date(parseISO(`${selectedMonth}-01`)).getDay();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-gray-900">School Calendar</h1>
        <p className="text-gray-500 text-sm">View upcoming school events and important dates</p>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-heading font-semibold text-gray-900 flex items-center gap-2">
            <HiCalendar className="w-5 h-5 text-primary-600" /> Events Calendar
          </h2>
          <input
            type="month"
            value={selectedMonth}
            onChange={(e) => setSelectedMonth(e.target.value)}
            className="input-field text-sm w-48"
          />
        </div>

        <div className="grid grid-cols-7 gap-1">
          {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((d) => (
            <div key={d} className="text-center text-xs font-semibold text-gray-500 py-2">{d}</div>
          ))}
          {Array.from({ length: startDay }).map((_, i) => (
            <div key={`empty-${i}`} />
          ))}
          {days.map((day) => {
            const dayEvents = getEventsForDay(day);
            return (
              <div
                key={day.toISOString()}
                className={`min-h-[60px] rounded-lg p-1 text-sm ${
                  dayEvents.length > 0
                    ? 'bg-primary-50 border border-primary-200'
                    : 'bg-gray-50'
                }`}
              >
                <span className={`font-medium text-xs ${
                  dayEvents.length > 0 ? 'text-primary-700' : 'text-gray-700'
                }`}>
                  {format(day, 'd')}
                </span>
                {dayEvents.length > 0 && (
                  <div className="mt-1">
                    {dayEvents.slice(0, 2).map((e) => (
                      <div key={e.id} className="text-[10px] leading-tight text-primary-600 truncate">
                        {e.title}
                      </div>
                    ))}
                    {dayEvents.length > 2 && (
                      <div className="text-[10px] text-primary-500">+{dayEvents.length - 2} more</div>
                    )}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>

      <div className="space-y-3">
        <h2 className="text-lg font-heading font-semibold text-gray-900">Upcoming Events</h2>
        {events.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <HiCalendar className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-500">No events scheduled for this month.</p>
          </div>
        ) : (
          events.map((e) => (
            <div key={e.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
              <div className="flex items-start gap-4">
                <div className="text-center bg-primary-50 rounded-lg px-3 py-2 min-w-[60px]">
                  <p className="text-lg font-bold text-primary-700">{format(parseISO(e.eventDate), 'dd')}</p>
                  <p className="text-xs text-primary-600">{format(parseISO(e.eventDate), 'MMM')}</p>
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="text-base font-heading font-semibold text-gray-900">{e.title}</h3>
                  {e.description && (
                    <p className="text-sm text-gray-600 mt-1">{e.description}</p>
                  )}
                  <div className="flex items-center gap-4 mt-2 text-xs text-gray-500">
                    {e.eventTime && (
                      <span className="flex items-center gap-1">
                        <HiClock className="w-3.5 h-3.5" /> {e.eventTime}
                      </span>
                    )}
                    {e.location && (
                      <span className="flex items-center gap-1">
                        <HiLocationMarker className="w-3.5 h-3.5" /> {e.location}
                      </span>
                    )}
                  </div>
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
}
