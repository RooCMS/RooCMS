<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'GNU General Public License RooCMS';
$page_description = 'GNU General Public License for RooCMS';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

        <section>
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/!/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">License</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">GNU General Public License</h1>
                <p class="mt-2 text-sm text-zinc-600">RooCMS license information and GPLv3 text</p>
            </header>

            <!-- License Content Container -->
            <div class="space-y-8">

                <!-- License Header -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold text-zinc-900">
                            GNU GENERAL PUBLIC LICENSE
                        </h2>
                        <p class="mt-2 text-sm text-zinc-600">
                            Version 3, 29 June 2007
                        </p>
                        <div class="mt-4 flex justify-center">
                            <span class="rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700">
                                GPLv3
                            </span>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" class="inline-flex items-center px-4 py-2 border border-zinc-300 text-sm font-medium rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View Full License
                            </a>
                        </div>
                    </div>
                </div>

                <!-- License Content -->
                <div class="space-y-6">

                    <!-- Preamble -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">Preamble</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="text-sm text-zinc-600 space-y-4">
                                <p>The GNU General Public License is a free, copyleft license for software and other kinds of works.</p>
                                <p>The licenses for most software and other practical works are designed to take away your freedom to share and change the works. By contrast, the GNU General Public License is intended to guarantee your freedom to share and change all versions of a program—to make sure it remains free software for all its users. We, the Free Software Foundation, use the GNU General Public License for most of our software; it applies also to any other work released this way by its authors. You can apply it to your programs, too.</p>
                                <p>When we speak of free software, we are referring to freedom, not price. Our General Public Licenses are designed to make sure that you have the freedom to distribute copies of free software (and charge for them if you wish), that you receive source code or can get it if you want it, that you can change the software or use pieces of it in new free programs, and that you know you can do these things.</p>
                                <p>To protect your rights, we need to prevent others from denying you these rights or asking you to surrender the rights. Therefore, you have certain responsibilities if you distribute copies of the software, or if you modify it: responsibilities to respect the freedom of others.</p>
                                <p>For example, if you distribute copies of such a program, whether gratis or for a fee, you must pass on to the recipients the same freedoms that you received. You must make sure that they, too, receive or can get the source code. And you must show them these terms so they know their rights.</p>
                                <p>Developers that use the GNU GPL protect your rights with two steps: (1) assert copyright on the software, and (2) offer you this License giving you legal permission to copy, distribute and/or modify it.</p>
                                <p>For the developers' and authors' protection, the GPL clearly explains that there is no warranty for this free software. For both users' and authors' sake, the GPL requires that modified versions be marked as changed, so that their problems will not be attributed erroneously to authors of previous versions.</p>
                                <p>Some devices are designed to deny users access to install or run modified versions of the software inside them, although the manufacturer can do so. This is fundamentally incompatible with the aim of protecting users' freedom to change the software. The systematic pattern of such abuse occurs in the area of products for individuals to use, which is precisely where it is most unacceptable. Therefore, we have designed this version of the GPL to prohibit the practice for those products. If such problems arise substantially in other domains, we stand ready to extend this provision to those domains in future versions of the GPL, as needed to protect the freedom of users.</p>
                                <p>Finally, every program is threatened constantly by software patents. States should not allow patents to restrict development and use of software on general-purpose computers, but in those that do, we wish to avoid the special danger that patents applied to a free program could make it effectively proprietary. To prevent this, the GPL assures that patents cannot be used to render the program non-free.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Key Points Summary -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">Key License Points</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-zinc-900">Your Rights:</h3>
                                    <ul class="space-y-2 text-sm text-zinc-600">
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Use:</strong> Run the software for any purpose</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Study:</strong> Access and examine the source code</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Share:</strong> Distribute copies to others</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Modify:</strong> Adapt the software to your needs</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="space-y-3">
                                    <h3 class="text-sm font-semibold text-zinc-900">Your Responsibilities:</h3>
                                    <ul class="space-y-2 text-sm text-zinc-600">
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Source Code:</strong> Make source code available when distributing</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>License:</strong> Include GPL license with copies</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Copyright:</strong> Preserve copyright notices</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Modifications:</strong> Mark modified versions as changed</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <blockquote class="rounded-xl border-l-4 border-sky-500 bg-sky-50 p-4 text-sm text-sky-900 mt-4">
                                <p><strong>Important:</strong> The GNU General Public License is designed to guarantee your freedom to share and change free software. It ensures that the software remains free for all its users.</p>
                            </blockquote>
                        </div>
                    </section>

                    <!-- RooCMS License Info -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">RooCMS License Information</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-semibold text-zinc-900">About RooCMS</h3>
                                        <p class="text-sm text-zinc-600">RooCMS is distributed under the GNU General Public License version 3 (GPLv3), ensuring that it remains free software for all users.</p>
                                        <div class="flex items-center space-x-4 text-xs text-zinc-500">
                                            <span>© 2010-<?php echo date('Y'); ?> alex Roosso</span>
                                            <span>Version: 2.0.0</span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-semibold text-zinc-900">License Compliance</h3>
                                        <p class="text-sm text-zinc-600">All RooCMS distributions include the full GPLv3 license text and maintain copyright notices as required.</p>
                                        <div class="flex items-center space-x-2">
                                            <span class="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">GPLv3 Compliant</span>
                                            <span class="rounded-md bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">Free Software</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-zinc-200 pt-4">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-zinc-600">
                                            For the complete license text, visit the official GNU website
                                        </div>
                                        <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" class="inline-flex items-center px-4 py-2 border border-zinc-300 text-sm font-medium rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            GNU GPL
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
?>
