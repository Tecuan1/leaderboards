import { Link } from '@inertiajs/react';
import GameLeaderboardController from '@/actions/App/Http/Controllers/GameLeaderboardController';

type Player = {
    rank: number;
    user_name: string;
    score: number;
};

type Game = {
    id: number;
    name: string;
    slug: string;
    top_players: Player[];
};

type Props = {
    games: Game[];
};

const rankMedal: Record<number, string> = {
    1: '🥇',
    2: '🥈',
    3: '🥉',
};

const rankColors: Record<number, string> = {
    1: 'from-yellow-400/20 to-yellow-600/5 border-yellow-500/30',
    2: 'from-slate-300/20 to-slate-500/5 border-slate-400/30',
    3: 'from-amber-600/20 to-amber-800/5 border-amber-700/30',
};

function PlayerRow({ player }: { player: Player }) {
    return (
        <div
            className={`flex items-center gap-3 rounded-xl border bg-gradient-to-r px-4 py-3 transition-all duration-200 hover:scale-[1.02] ${rankColors[player.rank] ?? 'from-white/5 to-white/0 border-white/10'}`}
        >
            <span className="w-8 text-center text-xl">{rankMedal[player.rank] ?? `#${player.rank}`}</span>
            <span className="flex-1 truncate font-semibold text-white">{player.user_name}</span>
            <span className="font-mono text-sm font-bold text-indigo-300 tabular-nums">
                {player.score.toLocaleString()}
            </span>
        </div>
    );
}

function GameCard({ game }: { game: Game }) {
    return (
        <div className="group flex flex-col rounded-2xl border border-white/10 bg-white/5 p-6 shadow-xl backdrop-blur-sm transition-all duration-300 hover:border-indigo-500/40 hover:bg-white/8 hover:shadow-indigo-500/10">
            <div className="mb-5 flex items-center justify-between">
                <Link
                    href={GameLeaderboardController.url(game.slug)}
                    className="text-xl font-bold text-white transition-colors duration-200 hover:text-indigo-400"
                    prefetch
                >
                    {game.name}
                </Link>
                <Link
                    href={GameLeaderboardController.url(game.slug)}
                    className="flex items-center gap-1 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300 transition-all duration-200 hover:border-indigo-400/50 hover:bg-indigo-500/20 hover:text-indigo-200"
                    prefetch
                >
                    Full board →
                </Link>
            </div>

            {game.top_players.length === 0 ? (
                <div className="flex flex-1 flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-white/10 py-8 text-center">
                    <span className="text-3xl">🎮</span>
                    <p className="text-sm text-slate-500">No scores yet. Be the first!</p>
                </div>
            ) : (
                <div className="flex flex-col gap-2">
                    {game.top_players.map((player) => (
                        <PlayerRow key={player.rank} player={player} />
                    ))}
                </div>
            )}
        </div>
    );
}

export default function Home({ games }: Props) {
    return (
        <div className="min-h-screen bg-[#0a0a12]">
            {/* Hero */}
            <div className="relative overflow-hidden">
                {/* Ambient glow */}
                <div className="pointer-events-none absolute inset-0 overflow-hidden">
                    <div className="absolute -top-40 left-1/2 h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-indigo-600/20 blur-[120px]" />
                    <div className="absolute top-20 left-1/4 h-[400px] w-[400px] rounded-full bg-violet-600/10 blur-[100px]" />
                    <div className="absolute top-10 right-1/4 h-[300px] w-[300px] rounded-full bg-cyan-600/10 blur-[100px]" />
                </div>

                <div className="relative mx-auto max-w-7xl px-4 pb-16 pt-20 text-center sm:px-6 lg:px-8">
                    <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-1.5 text-sm font-medium text-indigo-300">
                        <span className="relative flex h-2 w-2">
                            <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-75" />
                            <span className="relative inline-flex h-2 w-2 rounded-full bg-indigo-500" />
                        </span>
                        Live Leaderboards
                    </div>
                    <h1 className="mt-4 bg-gradient-to-br from-white via-slate-200 to-slate-400 bg-clip-text text-5xl font-extrabold tracking-tight text-transparent sm:text-6xl lg:text-7xl">
                        Game Rankings
                    </h1>
                    <p className="mx-auto mt-5 max-w-xl text-lg text-slate-400">
                        Top players across all games. Click a game to see the full leaderboard.
                    </p>
                </div>
            </div>

            {/* Games grid */}
            <main className="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
                {games.length === 0 ? (
                    <div className="flex flex-col items-center justify-center gap-4 py-32 text-center">
                        <span className="text-6xl">🏆</span>
                        <h2 className="text-2xl font-bold text-white">No games yet</h2>
                        <p className="text-slate-400">Check back soon — games will appear here.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {games.map((game) => (
                            <GameCard key={game.id} game={game} />
                        ))}
                    </div>
                )}
            </main>
        </div>
    );
}
