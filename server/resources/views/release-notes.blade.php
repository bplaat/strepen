@component('layouts.app')
    @slot('title', __('release-notes.title'))
    <div class="container">
        <h1 class="title">@lang('release-notes.header')</h1>

        <div class="box content">
            <h2 class="title is-4">Version 1.4.0-dev</h2>
            <p><i>In development</i></p>
            <ul>
                <li>Added release notes page</li>
                <li>Refactor small portions of the codebase</li>
                <li>Added casino page</li>
                <li>Add spinning wheel game</li>
                <li>Fixed many bugs</li>
            </ul>
        </div>

        <div class="box content">
            <h2 class="title is-4">Version 1.3.0</h2>
            <p><i>Released at 2022-05-17 with <a href="https://github.com/bplaat/strepen/commit/291c2d097279c66e5100596fdbf7bec0fa7ed626" target="_blank" rel="noreferrer">this commit</a></i></p>
            <ul>
                <li>Add arrow keys support to custom dropdown elements</li>
                <li>Food transactions are now generic payment transactions</li>
                <li>Add users export to text dialog</li>
                <li>Add PHP linter to the project for higher code quality control</li>
                <li>You can now like and dislikes post images</li>
                <li>Fixed many bugs</li>
            </ul>
        </div>

        <div class="box content">
            <h2 class="title is-4">Version 1.2.0</h2>
            <p><i>Released at 2022-01-16 with <a href="https://github.com/bplaat/strepen/commit/0f6ec4bf0753b20db4d08de09fca9bc8ee664f29" target="_blank" rel="noreferrer">this commit</a></i></p>
            <ul>
                <li>Refactor small portions of the codebase</li>
                <li>Added more routes to the REST API for the mobile app</li>
                <li>Posts can now contain images</li>
                <li>Added show dedicate post page for email links</li>
                <li>Made the layout of the website better</li>
                <li>Fixed many bugs</li>
            </ul>
        </div>

        <div class="box content">
            <h2 class="title is-4">Version 1.1.0</h2>
            <p><i>Released at 2021-10-30 with <a href="https://github.com/bplaat/strepen/commit/291b26282ec29dfc6eb5e5553ca8c09b1bcbf1be" target="_blank" rel="noreferrer">this commit</a></i></p>
            <ul>
                <li>Refactor small portions of the codebase</li>
                <li>Made the layout of the website better</li>
                <li>Fixed many bugs</li>
            </ul>
        </div>

        <div class="box content">
            <h2 class="title is-4">Version 0.5.0</h2>
            <p><i>Released at 2021-09-14 with <a href="https://github.com/bplaat/strepen/commit/fcd731f6926ab03640f60bf5c328942b8f8cc971" target="_blank" rel="noreferrer">this commit</a></i></p>
            <ul>
                <li>Created the new Strepen System website in Laravel with Livewire</li>
                <li>Auth system with accounts from previous strepen system</li>
                <li>Admin accounts can manage products and inventory and users can stripe products</li>
                <li>Users can view there past transactions and a graph of there balance history</li>
            </ul>
        </div>
    </div>
@endcomponent
