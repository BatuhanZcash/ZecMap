@php
    /** @var \App\Models\Community $community */
    /** @var array<string, int> $scores */
    $community = $row['model'];
    $scores = $row['scores'];
    $primaryScore = $scores[$period] ?? 0;
@endphp
<li class="communities-card">
    <div class="communities-card-rank">{{ $rank ?? ($loop->iteration ?? 1) }}</div>
    <div class="communities-card-main">
        <button type="button" class="communities-card-toggle" aria-expanded="false">
            <div class="communities-card-head">
                <span class="communities-card-flag" aria-hidden="true">{{ $community->flag_emoji ?: '🌍' }}</span>
                <div>
                    <h2 class="communities-card-name">{{ $community->name }}</h2>
                    @if ($community->description)
                        <p class="communities-card-desc">{{ $community->description }}</p>
                    @endif
                </div>
            </div>
            <span class="communities-card-toggle-meta">
                <span class="communities-card-period-score" aria-hidden="true">
                    {{ $community->formattedScore($primaryScore) }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-down communities-card-chevron" aria-hidden="true">
                    <path d="m6 9 6 6 6-6"></path>
                </svg>
            </span>
        </button>
        <div class="communities-card-scores-wrap">
            <div class="communities-card-scores">
                <div @class(['communities-score', 'communities-score--primary' => $period === 'weekly'])>
                    <span class="communities-score-label">Weekly</span>
                    <span class="communities-score-value">{{ $community->formattedScore($scores['weekly']) }}</span>
                </div>
                <div @class(['communities-score', 'communities-score--primary' => $period === 'monthly'])>
                    <span class="communities-score-label">Monthly</span>
                    <span class="communities-score-value">{{ $community->formattedScore($scores['monthly']) }}</span>
                </div>
                <div @class(['communities-score', 'communities-score--primary' => $period === 'yearly'])>
                    <span class="communities-score-label">Yearly</span>
                    <span class="communities-score-value">{{ $community->formattedScore($scores['yearly']) }}</span>
                </div>
                <div @class(['communities-score', 'communities-score--primary' => $period === 'all'])>
                    <span class="communities-score-label">Total</span>
                    <span class="communities-score-value">{{ $community->formattedScore($scores['all']) }}</span>
                </div>
            </div>
            <a class="communities-card-open" href="{{ route('communities.show', $community->slug) }}"
                style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-weight:600;font-size:.86rem;color:#C8921A;text-decoration:none">
                View businesses &amp; community page
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>
    </div>
</li>
