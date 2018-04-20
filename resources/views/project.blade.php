<div class="project"><img src="{{ $project->getThumb() }}" class="thumb">
    <div class="name"><a href="{{ $project->getUrl() }}">{{ $project->getName() }}</a></div>
    <div class="cost">{{ $project->getCost() }}</div>
    <div class="description">{{ $project->getDescription() }}</div>
    <div class="type"></div>
</div>