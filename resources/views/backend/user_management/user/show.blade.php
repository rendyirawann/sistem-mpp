@extends('backend.layout.app')
@section('title', 'User Detail')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar d-flex flex-stack py-4 py-lg-8">
        <!--begin::Toolbar wrapper-->
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">View User
                    Details</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a class="text-muted text-hover-primary">Home</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">User Management</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">Users</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-gray-900">View User</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

        </div>
        <!--end::Toolbar wrapper-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Layout-->
        <div class="d-flex flex-column flex-lg-row">
            <!--begin::Sidebar-->
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8 border border-gray-300">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Summary-->
                        <!--begin::User Info-->
                        <div class="d-flex flex-center flex-column py-5">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                @if ($data->avatar)
                                    <img src="{{ asset('storage/user/avatar/' . $data->avatar) }}"
                                        alt="{{ $data->name }}" />
                                @else
                                    <div class="symbol-label fs-2hx bg-light-primary text-primary">
                                        {{ strtoupper(substr($data->name, 0, 1)) }}
                                    </div>
                                @endif



                            </div>
                            <!--end::Avatar-->
                            <!--begin::Name-->
                            <a class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $data->name }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="mb-9">
                                <!--begin::Badge-->
                                @if (!empty($data->getRoleNames()))
                                    @foreach ($data->getRoleNames() as $v)
                                        <div class="badge badge-lg badge-light-primary d-inline">{{ $v }}</div>
                                    @endforeach
                                @endif
                                <!--begin::Badge-->
                            </div>
                            <!--end::Position-->

                        </div>
                        <!--end::User Info-->
                        <!--end::Summary-->

                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->






            </div>
            <!--end::Sidebar-->
            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-15">
                <!--begin:::Tabs-->
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                            href="#kt_user_view_overview_tab">Overview</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-kt-countup-tabs="true" data-bs-toggle="tab"
                            href="#kt_user_view_overview_security">Security</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                            href="#kt_user_view_overview_activity">Activity</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                            href="#kt_user_view_overview_login_sessions">Login Sessions</a>
                    </li>
                    <!--end:::Tab item-->

                </ul>
                <!--end:::Tabs-->
                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">

                        <!--begin::Tasks-->
                        <div class="card border border-gray-300">
                            <!--begin::Card header-->
                            <div class="card-header border-bottom border-gray-300">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Profile Detail</h2>
                                </div>
                                <!--end::Card title-->

                            </div>
                            <!--end::Card header-->
                            <div class="separator"></div>
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed gy-5"
                                        id="kt_table_users_login_session">
                                        <tbody class="fs-6 fw-semibold text-gray-600">



                                            <tr>
                                                <td>Name</td>
                                                <td>{{ $data->name }}</td>

                                            </tr>

                                            <tr>
                                                <td>Email</td>
                                                <td>{{ $data->email }}</td>

                                            </tr>

                                            <tr>
                                                <td>WhatsApp</td>
                                                <td>{{ $data->no_wa }}</td>

                                            </tr>





                                        </tbody>
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Tasks-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_overview_security" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card border border-gray-300">
                            <!--begin::Card header-->
                            <div class="card-header border-bottom border-gray-300">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Security Detail</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <div class="separator"></div>
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed gy-5"
                                        id="kt_table_users_login_session">
                                        <tbody class="fs-6 fw-semibold text-gray-600">
                                            <tr>
                                                <td>Email</td>
                                                <td>{{ $data->email }}</td>

                                            </tr>

                                            <tr>
                                                <td>Role</td>
                                                <td>
                                                    @if ($data->getRoleNames()->isNotEmpty())
                                                        @foreach ($data->getRoleNames() as $v)
                                                            {{ $v }}
                                                        @endforeach
                                                    @else
                                                        No roles assigned
                                                    @endif
                                                </td>

                                            </tr>

                                            <tr>
                                                <td>Joined date</td>
                                                <td>
                                                    {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i') : 'Never created' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Last Login at</td>
                                                <td>
                                                    {{ $data->last_login ? \Carbon\Carbon::parse($data->last_login)->translatedFormat('d F Y, H:i') : 'Never logged in' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Last Login IP Address</td>
                                                <td>
                                                    {{ $data->last_ip ? $data->last_ip : 'N/A' }}
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->


                    </div>
                    <!--end:::Tab pane-->
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_overview_activity" role="tabpanel">

                        <!--begin::Card-->
                        <div class="card mb-6 mb-xl-9 border border-gray-300">
                            <!--begin::Card header-->
                            <div class="card-header border-bottom border-gray-300">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Activity</h2>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Button-->

                                    <!--end::Button-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <div class="separator"></div>
                            <!--begin::Card body-->
                            <div class="card-body py-0">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table
                                        class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5 chimox_logs"
                                        id="chimox_logs">
                                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                            <tr class="text-start text-muted text-uppercase gs-0">
                                                <th class="min-w-100px">IP Address</th>
                                                <th class="min-w-100px">Device</th>
                                                <th class="min-w-100px">Operating System</th>
                                                <th class="min-w-100px">Description</th>
                                                <th class="min-w-100px text-end">Time</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                    </div>
                    <!--end:::Tab pane-->


                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_overview_login_sessions" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-6 mb-xl-9 border border-gray-300">
                            <!--begin::Card header-->
                            <div class="card-header border-bottom border-gray-300">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Login Sessions</h2>
                                </div>
                                <!--end::Card title-->

                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Filter-->

                                    <!--end::Filter-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <div class="separator"></div>
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed gy-5 chimox" id="chimox">
                                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                            <tr class="text-start text-muted text-uppercase gs-0">
                                                <th class="min-w-100px">IP Address</th>
                                                <th class="min-w-100px">Description</th>
                                                <th class="min-w-100px">Device</th>
                                                <th class="min-w-100px">Operating System</th>
                                                <th class="text-end min-w-100px">Time</th>
                                            </tr>
                                        </thead>

                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->


                    </div>
                    <!--end:::Tab pane-->
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Layout-->

    </div>
    <!--end::Content-->

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {


                // init datatable.
                var dataTable = $('.chimox').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: true,
                    info: true,
                    ajax: {
                        url: "{{ route('get-user-show-log', ['id' => $data->id]) }}",
                        type: 'GET',
                    },
                    columns: [{
                            data: 'ip',
                            name: 'ip',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'device',
                            name: 'device',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'os',
                            name: 'os',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                    ]

                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {


                // init datatable.
                var dataTable = $('.chimox_logs').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: true,
                    info: true,
                    ajax: {
                        url: "{{ route('get-user-show-log-activity', ['id' => $data->id]) }}",
                        type: 'GET',
                    },
                    columns: [{
                            data: 'ip',
                            name: 'ip',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'device',
                            name: 'device',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'os',
                            name: 'os',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                    ]

                });
            });
        </script>
    @endpush
@endsection
