<?php

namespace App\View\Components;

use App\Models\Workflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class PageNavigation extends Component
{
    /** @var array<int, array{label: string, url: ?string}> */
    public array $items;

    /** @var array{label: string, url: ?string}|null */
    public ?array $parent;

    public function __construct(Request $request, ?array $items = null, ?string $currentLabel = null)
    {
        $this->items = $items ?? $this->trail($request, $currentLabel);
        $this->parent = count($this->items) > 1 ? $this->items[count($this->items) - 2] : null;
    }

    private function trail(Request $request, ?string $currentLabel): array
    {
        $route = $request->route();
        $name = $route?->getName();
        $system = $this->link('System', 'system.index');

        // Explicit parents keep direct visits and post-save reloads predictable.
        $sections = [
            'workflows' => ['Workflows', 'Workflow', true],
            'role' => ['Roles', 'Role', true],
            'document_requirements' => ['Document Requirements', 'Document', true],
            'funeral_assistance_types' => ['Funeral Assistance Types', 'Assistance Type', true],
            'mode_of_assistances' => ['Modes of Assistance', 'Mode of Assistance', true],
            'application' => ['Applications', 'Application', false],
            'client' => ['Clients', 'Client', false],
            'beneficiary' => ['Beneficiaries', 'Beneficiary', false],
        ];

        foreach ($sections as $prefix => [$plural, $singular, $administrative]) {
            $index = $this->link($plural, $prefix.'.index');
            $ancestors = $administrative ? [$system] : [];

            if ($name === $prefix.'.index') {
                return $administrative ? [$system, $this->current($plural)] : [];
            }

            if ($name === $prefix.'.create') {
                return [...$ancestors, $index, $this->current($currentLabel ?? 'Create '.$singular)];
            }

            if ($name === $prefix.'.show' || $name === $prefix.'.edit') {
                $ancestors[] = $index;
                // These resources have a details page that is the edit page's parent.
                if ($name === $prefix.'.edit' && in_array($prefix, ['client', 'beneficiary'], true)) {
                    $ancestors[] = $this->link($singular.' details', $prefix.'.show', $route->parameter($prefix));
                }

                return [...$ancestors, $this->current($currentLabel ?? ($name === $prefix.'.edit' ? 'Edit '.$singular : $singular.' details'))];
            }
        }

        if ($name === 'workflows.stage.edit') {
            $workflow = $route->parameter('workflow');

            return [$system, $this->link('Workflows', 'workflows.index'),
                $this->link($workflow instanceof Workflow ? $workflow->label() : 'Workflow', 'workflows.edit', $workflow),
                $this->current($currentLabel ?? 'Edit Stage')];
        }

        if (in_array($name, ['application.assessment.create', 'application.recommendation.create'], true)) {
            return [$this->link('Applications', 'application.index'),
                $this->link('Application details', 'application.show', $route->parameter('application')),
                $this->current($name === 'application.assessment.create' ? 'Create Assessment' : 'Create Recommendation')];
        }

        if ($name === 'application.search') {
            return [$this->link('Applications', 'application.index'), $this->current('Search Applications')];
        }

        if ($name === 'beneficiary.family.edit') {
            $member = $route->parameter('member');

            return [$this->link('Beneficiaries', 'beneficiary.index'),
                $this->link('Beneficiary details', 'beneficiary.show', $member->beneficiary_uuid),
                $this->current($currentLabel ?? 'Edit Family Member')];
        }

        if ($name === 'user.index') {
            return [$system, $this->current('Users')];
        }

        if ($name === 'user.show' || $name === 'user.edit') {
            // The users index is restricted to superadmins; account pages are not.
            $ancestors = $request->user()?->hasRole('superadmin')
                ? [$system, $this->link('Users', 'user.index')]
                : [$this->link('Dashboard', 'dashboard')];

            // UserController::show has a separate authorization rule, so edits
            // return to the accessible section rather than assuming show access.
            return [...$ancestors, $this->current($currentLabel ?? ($name === 'user.edit' ? 'Edit Profile' : 'Profile'))];
        }

        return match ($name) {
            'system.settings' => [$system, $this->current('Settings')],
            'system.logs.index' => [$system, $this->current('Logs')],
            'system.logs.show' => [$system, $this->link('Logs', 'system.logs.index'), $this->current('Log details')],
            default => [],
        };
    }

    private function link(string $label, string $route, mixed $parameters = []): array
    {
        return ['label' => $label, 'url' => route($route, $parameters)];
    }

    private function current(string $label): array
    {
        return ['label' => $label, 'url' => null];
    }

    public function render(): View
    {
        return view('components.page-navigation');
    }
}
