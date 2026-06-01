<?php

namespace Incrudible\Incrudible;

use App\Incrudible\Models\Admin;
use Illuminate\Support\Facades\Auth;

class Incrudible
{
    /**
     * Check if the incrudible config was published.
     */
    public function configNotPublished(): bool
    {
        return is_null(config('incrudible'));
    }

    /**
     * Get the currently authenticated admin.
     */
    public function admin(): ?Admin
    {
        $admin = Auth::guard(self::guardName())->user();

        return $admin instanceof Admin ? $admin : null;
    }

    /**
     * Determine if the current admin is authenticated.
     */
    public function check(): bool
    {
        return Auth::guard(self::guardName())->check();
    }

    /**
     * Get the incrudible route prefix.
     */
    public function routePrefix(): string
    {
        // dd(config('incrudible.route_prefix'))
        return config('incrudible.route_prefix', 'incrudible');
    }

    /**
     * Get the incrudible middleware identifier.
     */
    public function middleware(): string
    {
        return config('incrudible.auth.middleware_key', 'incrudible');
    }

    /**
     * Get the incrudible middleware classes.
     */
    public function middlewareClasses(): array
    {
        return config('incrudible.auth.middleware_classes', []);
    }

    /**
     * Get the incrudible authentication guard name.
     */
    public function guardName(): string
    {
        return config('incrudible.auth.guard', 'incrudible');
    }

    /**
     * Get the incrudible menu items.
     */
    public function menu(): array
    {
        return config('incrudible.menu', []);
    }

    /**
     * Resolve menu route names to URLs before sharing them with the browser.
     */
    private function menuWithUrls(array $menu): array
    {
        if (isset($menu['items']) && is_array($menu['items'])) {
            $menu['items'] = $this->menuItemsWithUrls($menu['items']);
        }

        if (isset($menu['top_right_items']) && is_array($menu['top_right_items'])) {
            $menu['top_right_items'] = $this->menuItemsWithUrls($menu['top_right_items']);
        }

        return $menu;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function menuItemsWithUrls(array $items): array
    {
        return array_map(function (array $item): array {
            if (isset($item['route']) && is_string($item['route'])) {
                $item['url'] = $this->menuRouteUrl($item['route']);
            }

            if (isset($item['items']) && is_array($item['items'])) {
                $item['items'] = $this->menuItemsWithUrls($item['items']);
            }

            return $item;
        }, $items);
    }

    private function menuRouteUrl(string $route): ?string
    {
        try {
            return route(self::routePrefix().'.'.$route, [], false);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Convert this Incrudible class to an array.
     */
    public function toArray(): array
    {
        return [
            'routePrefix' => self::routePrefix(),
            'currentRouteName' => request()->route()?->getName(),
            'currentUrl' => request()->getRequestUri(),
            'menu' => $this->menuWithUrls(self::menu()),
        ];
    }
}
