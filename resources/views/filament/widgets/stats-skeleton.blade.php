<div>
    <style>
        @keyframes skeleton-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .skel-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 768px) { .skel-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .skel-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

        .skel-stat-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: skeleton-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .skel-line { background-color: #e5e7eb; border-radius: 0.375rem; }
        :is(.dark) .skel-stat-card { background-color: #18181b; border-color: rgba(255, 255, 255, 0.05); }
        :is(.dark) .skel-line { background-color: #27272a; }
    </style>

    <div class="skel-grid">
        @for($i = 0; $i < 4; $i++)
            <div class="skel-stat-card">
                <div class="skel-line" style="height: 1rem; width: 50%; margin-bottom: 1rem;"></div>
                <div class="skel-line" style="height: 2rem; width: 75%; margin-bottom: 0.5rem;"></div>
                <div class="skel-line" style="height: 0.75rem; width: 25%; margin-top: auto;"></div>
            </div>
        @endfor
    </div>
</div>
