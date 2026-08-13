import { useState } from 'react';
import { Link } from '@inertiajs/react';
import HomeController from '@/actions/App/Http/Controllers/HomeController';

type Player = {
    rank: number;
    user_name: string;
    score: number;
};

type Leaderboards = {
    today: Player[];
    weekly: Player[];
    monthly: Player[];
    all: Player[];
};

type Game = {
    id: number;
    name: string;
    slug: string;
};

type Props = {
    game: Game;
    leaderboards: Leaderboards;
};

type Period = keyof Leaderboards;

const PERIODS: { key: Period; label: string; icon: string }[] = [
    { key: 'today', label: 'Today', icon: '☀️' },
    { key: 'weekly', label: 'This Week', icon: '📅' },
    { key: 'monthly', label: 'This Month', icon: '🗓️' },
    { key: 'all', label: 'All Time', icon: '🏆' },
];

const rankMedal: Record<number, string> = { 1: '🥇', 2: '🥈', 3: '🥉' };

const rankBg: Record<number, string> = {
    1: 'from-yellow-400/20 to-yellow-600/5 border-yellow-500/30',
    2: 'from-slate-300/20 to-slate-500/5 border-slate-400/30',
    3: 'from-amber-600/20 to-amber-800/5 border-amber-700/30',
};

function LeaderboardTable({ players }: { players: Player[] }) {
    if (players.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-white/10 py-16 text-center">
                <span className="text-5xl">😶</span>
                <p className="text-slate-400">No scores in this period yet.</p>
            </div>
        );
    }

    return (
        <div className="overflow-hidden rounded-2xl border border-white/10">
            {/* Header */}
            <div className="grid grid-cols-[3rem_1fr_auto] gap-4 border-b border-white/10 bg-white/5 px-6 py-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
                <span className="text-center">#</span>
                <span>Player</span>
                <span>Score</span>
            </div>

            {/* Rows */}
            <div className="divide-y divide-white/5">
                {players.map((player) => (
                    <div
                        key={player.rank}
                        className={`grid grid-cols-[3rem_1fr_auto] items-center gap-4 px-6 py-4 transition-all duration-150 hover:bg-white/5 ${player.rank <= 3 ? `bg-gradient-to-r ${rankBg[player.rank]}` : ''}`}
                    >
                        <span className="text-center text-lg">
                            {rankMedal[player.rank] ?? (
                                <span className="font-mono text-sm text-slate-500">{player.rank}</span>
                            )}
                        </span>
                        <span className="truncate font-semibold text-white">{player.user_name}</span>
                        <span className="font-mono text-sm font-bold text-indigo-300 tabular-nums">
                            {player.score.toLocaleString()}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default function GameLeaderboard({ game, leaderboards }: Props) {
    const [activePeriod, setActivePeriod] = useState<Period>('all');
    const players = leaderboards[activePeriod];

    return (
        <div className="min-h-screen bg-[#0a0a12]">
            {/* Ambient glow */}
            <div className="pointer-events-none fixed inset-0 overflow-hidden">
                <div className="absolute -top-40 left-1/2 h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-indigo-600/15 blur-[120px]" />
                <div className="absolute bottom-0 right-0 h-[400px] w-[400px] rounded-full bg-violet-600/10 blur-[100px]" />
            </div>

            <div className="relative mx-auto max-w-3xl px-4 py-12 sm:px-6">
                {/* Back link */}
                <Link
                    href={HomeController.url()}
                    className="mb-8 inline-flex items-center gap-2 text-sm text-slate-400 transition-colors duration-200 hover:text-white"
                >
                    ← All Games
                </Link>

                {/* Header */}
                <div className="mb-10 text-center">
                    <div className="mb-3 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-1.5 text-sm font-medium text-indigo-300">
                        🎮 {game.name}
                    </div>
                    <h1 className="bg-gradient-to-br from-white via-slate-200 to-slate-400 bg-clip-text text-4xl font-extrabold tracking-tight text-transparent sm:text-5xl">
                        Leaderboard
                    </h1>
                </div>

                {/* Period tabs */}
                <div className="mb-8 flex items-center gap-2 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 p-1.5 backdrop-blur-sm">
                    {PERIODS.map(({ key, label, icon }) => (
                        <button
                            key={key}
                            id={`tab-${key}`}
                            type="button"
                            onClick={() => setActivePeriod(key)}
                            className={`flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 ${
                                activePeriod === key
                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25'
                                    : 'text-slate-400 hover:bg-white/5 hover:text-white'
                            }`}
                        >
                            <span>{icon}</span>
                            <span>{label}</span>
                            {leaderboards[key].length > 0 && (
                                <span
                                    className={`rounded-full px-1.5 py-0.5 text-xs font-bold ${
                                        activePeriod === key ? 'bg-white/20 text-white' : 'bg-white/10 text-slate-400'
                                    }`}
                                >
                                    {leaderboards[key].length}
                                </span>
                            )}
                        </button>
                    ))}
                </div>

                {/* Leaderboard table */}
                <LeaderboardTable players={players} />
            </div>
        </div>
    );
}
