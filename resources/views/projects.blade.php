@extends('layouts.app')

@section('title', 'All Projects — Kristian Hernandez')

@section('mobile_menu_links')
  <a href="{{ route('home') }}#about" class="mm-link">About</a>
  <a href="{{ route('home') }}#projects" class="mm-link">Projects</a>
  <a href="{{ route('home') }}#skills" class="mm-link">Skills</a>
  <a href="{{ route('home') }}#certifications" class="mm-link">Certifications</a>
  <a href="{{ route('home') }}#experience" class="mm-link">Experience</a>
  <a href="{{ route('home') }}#contact" class="mm-link">Contact</a>
@endsection

@section('navigation')
  <x-nav />
@endsection

@section('content')
<section id="projects" style="padding-top: clamp(7rem, 10vw, 9rem); min-height: 90vh;">
  <div class="s-in" style="padding-top: 0; padding-bottom: 4.5rem;">
    <div class="proj-header" style="margin-bottom: 3rem;">
      <div class="r in">
        <div class="s-eyebrow">02 &mdash; Portfolio</div>
        <h1 class="s-head">ALL<br>PROJECTS.</h1>
      </div>
    </div>

    <div class="proj-grid">
      @forelse ($projects as $project)
        <x-project-card :project="$project" :loop="$loop" />
      @empty
        <div class="empty-state">Projects will appear here once they are published from the CMS.</div>
      @endforelse
    </div>
  </div>
</section>
@endsection
