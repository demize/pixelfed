<?php

namespace App\Providers;

use App\Observers\{
	AvatarObserver,
	FollowerObserver,
	LikeObserver,
	NotificationObserver,
	ModLogObserver,
	ProfileObserver,
    StatusHashtagObserver,
    StatusObserver,
	UserObserver,
	UserFilterObserver,
};
use App\{
	Avatar,
	Follower,
	Like,
	Notification,
	ModLog,
	Profile,
	StatusHashtag,
    Status,
	User,
	UserFilter
};
use Auth, Horizon, URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNClient;
use League\Flysystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if(config('instance.force_https_urls', true)) {
			URL::forceScheme('https');
		}

		Storage::extend('bunnycdn', function ($app, $config) {
			$adapter = new BunnyCDNAdapter(
				new BunnyCDNClient(
					$config['storage_zone'],
					$config['api_key'],
					$config['region']
				),
				$config['pull_zone']
			);

			return new FilesystemAdapter(
				new Filesystem($adapter, $config),
				$adapter,
				$config
			);
		});

		Schema::defaultStringLength(191);
		Paginator::useBootstrap();
		Avatar::observe(AvatarObserver::class);
		Follower::observe(FollowerObserver::class);
		Like::observe(LikeObserver::class);
		Notification::observe(NotificationObserver::class);
		ModLog::observe(ModLogObserver::class);
		Profile::observe(ProfileObserver::class);
		StatusHashtag::observe(StatusHashtagObserver::class);
		User::observe(UserObserver::class);
        Status::observe(StatusObserver::class);
		UserFilter::observe(UserFilterObserver::class);
		Horizon::auth(function ($request) {
			return Auth::check() && $request->user()->is_admin;
		});
		Validator::includeUnvalidatedArrayKeys();
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
