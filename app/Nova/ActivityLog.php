<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActivityLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Spatie\Activitylog\Models\Activity::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Log', function(){
                if ($this->causer_id) {
                    return sprintf('%s -  %s was %s by %s %s (%s)',
                        $this->subject_type,
                        $this->subject_id,
                        $this->description,
                        $this->causer->first_name,
                        $this->causer->surname,
                        $this->causer->email
                    );
                }

                return sprintf('%s -  %s was %s by Anonymous',
                    $this->subject_type,
                    $this->subject_id,
                    $this->description
                );
            }),
            Text::make('Causer Type')->onlyOnDetail(),
            Text::make('Causer ID', 'causer_id')->onlyOnDetail(),
            Text::make('Caused By', function() {
                $path = sprintf('/resources/%s/%s',
                    Str::kebab(Str::plural(class_basename($this->causer_type))),
                    $this->causer_id
                );

                return sprintf('<strong><a href="%s">%s %s</a></strong>',
                    $path,
                    class_basename($this->causer_type),
                    $this->causer_id
                );
            })->asHtml(),
            Text::make('Subject Type')->onlyOnDetail(),
            Text::make('Subject ID', 'subject_id')->onlyOnDetail(),
            Text::make('Subject', function() {
                $path = sprintf('/resources/%s/%s',
                    Str::kebab(Str::plural(class_basename($this->subject_type))),
                    $this->subject_id
                );

                return sprintf('<strong><a href="%s">%s %s</a></strong>',
                    $path,
                    class_basename($this->subject_type),
                    $this->subject_id
                );
            })->asHtml(),
            Text::make('Log Name')->onlyOnDetail(),
            Text::make('Description')->onlyOnDetail(),
            DateTime::make('Date', 'created_at'),
            Code::make('Properties')->json()->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
