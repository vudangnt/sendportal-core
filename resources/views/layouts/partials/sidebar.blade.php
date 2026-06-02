<div class="sidebar-inner mx-3">
    <ul class="nav flex-column mt-4">
        <li class="nav-item {{ request()->routeIs('sendportal.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.dashboard') }}">
                <i class="fa-fw fas fa-home mr-2"></i><span>{{ __('Dashboard') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*campaigns*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.campaigns.sent') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Campaigns') }}</span>
            </a>
        </li>
        @if (\Sendportal\Base\Facades\Helper::isPro())
        <li class="nav-item {{ request()->is('*automations*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.automations.index') }}">
                <i class="fa-fw fas fa-sync-alt mr-2"></i><span>{{ __('Automations') }}</span>
            </a>
        </li>
        @endif
        <li class="nav-item {{ request()->is('templates') || (request()->is('templates/*') && !request()->is('templates/transactional*')) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.templates.index') }}">
                <i class="fa-fw fas fa-file-alt mr-2"></i><span>{{ __('Templates') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('templates/transactional*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.templates.transactional.index') }}">
                <i class="fa-fw fas fa-paper-plane mr-2"></i><span>{{ __('Transactional Templates') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*subscribers*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.subscribers.index') }}">
                <i class="fa-fw fas fa-user mr-2"></i><span>{{ __('Subscribers') }}</span>
            </a>
        </li>

        {{-- Tag Types Management --}}
        <li class="nav-item sidebar-section-header mt-2">
            <span class="nav-link text-muted small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                <i class="fa-fw fas fa-cogs mr-1"></i>{{ __('Manage Tags') }}
            </span>
        </li>
        <li class="nav-item {{ request()->is('*tags*') ? 'active' : '' }}">
            <a class="nav-link py-1" href="{{ route('sendportal.tags.index') }}">
                <i class="fa-fw fas fa-tags mr-2"></i><span>{{ __('Tags') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*locations*') ? 'active' : '' }}">
            <a class="nav-link py-1" href="{{ route('sendportal.locations.index') }}">
                <i class="fa-fw fas fa-map-marker-alt mr-2"></i><span>{{ __('Locations') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*skills*') ? 'active' : '' }}">
            <a class="nav-link py-1" href="{{ route('sendportal.skills.index') }}">
                <i class="fa-fw fas fa-tools mr-2"></i><span>{{ __('Skills') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*industries*') ? 'active' : '' }}">
            <a class="nav-link py-1" href="{{ route('sendportal.industries.index') }}">
                <i class="fa-fw fas fa-industry mr-2"></i><span>{{ __('Industries') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*levels*') ? 'active' : '' }}">
            <a class="nav-link py-1" href="{{ route('sendportal.levels.index') }}">
                <i class="fa-fw fas fa-layer-group mr-2"></i><span>{{ __('Levels') }}</span>
            </a>
        </li>

        <li class="nav-item mt-2 {{ request()->is('*messages*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.messages.index') }}">
                <i class="fa-fw fas fa-paper-plane mr-2"></i><span>{{ __('Messages') }}</span>
            </a>
        </li>
        @if(config('sendportal-host.email_services.show_menu', true))
        <li class="nav-item {{ request()->is('*email-services*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.email_services.index') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Email Services') }}</span>
            </a>
        </li>
        @endif

        {!! \Sendportal\Base\Facades\Sendportal::sidebarHtmlContent() !!}

    </ul>
</div>
