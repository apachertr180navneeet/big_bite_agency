<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="app-brand-logo demo">
				<img src="{{asset('assets/admin/img/bigbitelogo.png')}}" width="35%"/>
			</span>
			{{--  <span class="app-brand-text demo menu-text fw-bold ms-2">Big Bite Agency</span>  --}}
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('admin.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>

		
		@foreach([
			['route' => 'admin.salesparsonmanagment.index', 'text' => 'Sales Parson'],
			['route' => 'admin.customer.index', 'text' => 'Customer'],
			['route' => 'admin.invoice.index', 'text' => 'Invoice'],
			['route' => 'admin.receipt.index', 'text' => 'Receipt'],
			['route' => 'admin.reports.sale.person', 'text' => 'Sale Person Report'],
			['route' => 'admin.reports.un.claim.report', 'text' => 'UN Claim Report'],
		] as $mastermenu)
			<li class="menu-item {{ request()->routeIs($mastermenu['route']) ? 'active' : '' }}">
				<a href="{{ route($mastermenu['route']) }}" class="menu-link">
					<i class="menu-icon tf-icons"></i>
					<div data-i18n="{{ $mastermenu['text'] }}">{{ $mastermenu['text'] }}</div>
				</a>
			</li>
		@endforeach
		
	</ul>
</aside>