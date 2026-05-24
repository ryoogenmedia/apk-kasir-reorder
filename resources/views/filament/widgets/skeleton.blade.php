<div>
    <style>
        @keyframes skeleton-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .skel-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1rem;
            animation: skeleton-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .skel-line { background-color: #e5e7eb; border-radius: 0.375rem; }
        .skel-circle { background-color: #e5e7eb; border-radius: 9999px; }
        :is(.dark) .skel-card { background-color: #18181b; border-color: rgba(255, 255, 255, 0.05); }
        :is(.dark) .skel-line, :is(.dark) .skel-circle { background-color: #27272a; }
    </style>

    <div class="skel-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div class="skel-line" style="height: 1.25rem; width: 33.333333%;"></div>
            <div class="skel-circle" style="height: 2rem; width: 2rem;"></div>
        </div>
        <div class="skel-line" style="height: 2.5rem; width: 66.666667%; margin-top: 0.5rem; margin-bottom: 0.5rem;"></div>
        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <div class="skel-line" style="height: 0.75rem; width: 100%;"></div>
            <div class="skel-line" style="height: 0.75rem; width: 83.333333%;"></div>
        </div>
    </div>
</div>
