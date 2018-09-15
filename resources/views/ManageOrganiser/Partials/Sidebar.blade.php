<aside class="sidebar sidebar-left sidebar-menu">
    <section class="content">
        <h5 class="heading">@lang("Organiser.organiser_menu")</h5>

        <ul id="nav" class="topmenu">
            @if(auth()->user()->isSuperAdmin())
                <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserDashboard', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-home2"></i></span>
                        <span class="text">@lang("Organiser.dashboard")</span>
                    </a>
                </li>
                <li class="{{ Request::is('*events*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserEvents', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-calendar"></i></span>
                        <span class="text">@lang("Organiser.event")</span>
                    </a>
                </li>

                <li class="{{ Request::is('*coupons*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserCoupons', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-money"></i></span>
                        <span class="text">@lang("Organiser.coupons")</span>
                    </a>
                </li>

                <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserCustomize', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-cog"></i></span>
                        <span class="text">@lang("Organiser.customize")</span>
                    </a>
                </li>
            @else
                <li class="{{ Request::is('*events*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserEvents', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-calendar"></i></span>
                        <span class="text">@lang("Organiser.event")</span>
                    </a>
                </li>
            @endif
        </ul>
    </section>
</aside>
