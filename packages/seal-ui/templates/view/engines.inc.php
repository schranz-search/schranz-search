<?php
/**
 * @var iterable<string, array{
 *     title: string,
 *     url: string,
 *     active: bool,
 *     indexes: iterable<string, array{
 *          title: string,
 *          url: string,
 *          active: bool,
 *     }>,
 * }> $engines
 * @var \Schranz\Search\SEAL\Search\Result $result
 */

$base = require \dirname(__DIR__) . '/base.inc.php';;

$base(function() use ($engines, $result): void {
?>
    <nav style="padding: 10px 0; display: block; background: #202020; width: 196px; position: absolute; left: 0; top: 0; height: 100vh; box-shadow: 0 0 24px #666;">
        <ul style="display: grid; list-style: none; margin: 0; padding: 0; gap: 5px;">
            <?php foreach ($engines as $name => $engine): ?>
                <?php foreach ($engine['indexes'] as $name => $index): ?>
                    <li style="padding: 0; margin: 0;">
                        <a href="<?php echo $index['url']; ?>" style="display: block; color: white; padding: 10px 20px; text-decoration: none;">
                            <?php echo $index['title']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div style="margin-left: 196px; padding: 20px; overflow-x: scroll; min-height: 100vh; background: #eee;">
        <?php if ($result !== null) {
            $keys = [];
            $documents = [...$result];
            foreach ($documents as $document) {
                $keys = \array_merge($keys, \array_keys($document));
                $keys =\array_unique($keys);
            }
        ?>
            <form method="get" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="query" style="font-weight: bold; display: block;">
                        Query
                    </label>

                    <input autofocus type="search" name="query" id="query" value="<?php echo escape($_GET['query'] ?? ''); ?>" style="border-radius: 4px; border: 2px solid #ccc; display: blocK; padding: 12px 12px;">
                </div>

                <div>
                    <input type="hidden" name="engine" value="<?php echo escape($_GET['engine'] ?? ''); ?>">
                    <input type="hidden" name="index" value="<?php echo escape($_GET['index'] ?? ''); ?>">

                    <button type="submit" style="padding: 12px 24px; border: none; background: #fff; border: 2px solid #ccc; font-weight: bold; border-radius: 4px; width: auto;">
                        Search
                    </button>
                </div>
            </form>

            <hr style="display: block; margin: 32px 0 24px; border: 0; padding: 0; background: #ccc; height: 2px;" />

            <h1 style="margin: 0;">
                <?php if ($result->total() > 0): ?>
                    Results (<?php echo $result->total() ?>)
                <?php else: ?>
                    No results
                <?php endif; ?>
            </h1>

            <?php if ($result->total() > 0): ?>
                <div style="display: block; overflow-x: auto; scrollbar-width: thin;">
                    <table style="margin-top: 24px; width: 100%; text-align: left; border-spacing: 0 4px;">
                        <thead>
                            <tr style="box-shadow: 0 0 24px #ccc;">
                                <?php foreach ($keys as $keyCount => $key): ?>
                                    <?php
                                        $borderWidth = match(true) {
                                            $keyCount === 0 => '1px 0 1px 1px',
                                            ($keyCount + 1) === \count($keys) => '1px 1px 1px 0',
                                            default => '1px 0 1px 0',
                                        };
                                        $borderRadius = match(true) {
                                            $keyCount === 0 => '4px 0 0 4px',
                                            ($keyCount + 1) === \count($keys) => '0 4px 4px 0',
                                            default => '0',
                                        };
                                    ?>
                                    <th style="text-transform: uppercase; padding: 12px 16px; background: white; border: none; border: 1px solid #333; border-radius: <?php echo $borderRadius; ?>; border-width: <?php echo $borderWidth; ?>;"><?php echo $key; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $count = 0; ?>
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <?php ++$count; ?>
                                    <?php foreach ($keys as $indexCount => $key): ?>
                                        <?php
                                            $borderWidth = match(true) {
                                                $indexCount === 0 => '1px 0 1px 1px',
                                                ($indexCount + 1) === \count($keys) => '1px 1px 1px 0',
                                                default => '1px 0 1px 0',
                                            };
                                            $borderRadius = match(true) {
                                                $indexCount === 0 => '4px 0 0 4px',
                                                ($indexCount + 1) === \count($keys) => '0 4px 4px 0',
                                                default => '0',
                                            };
                                            $borderColor = match(true) {
                                                $count % 2 === 0 => '#999',
                                                default => '#999',
                                            };
                                            $background = match(true) {
                                                $count % 2 === 0 => 'white',
                                                default => '#f3f3f3',
                                            };
                                        ?>
                                        <td style="padding: 12px 16px; border: none; border: 2px solid <?php echo $borderColor ?>; border-radius: <?php echo $borderRadius; ?>; border-width: <?php echo $borderWidth; ?>; background: <?php echo $background; ?>;">
                                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 200px;">
                                                <?php
                                                    $output = $document[$key] ?? '';
                                                    if (!\is_string($output)
                                                        && !\is_numeric($output)
                                                        && !\is_bool($output)
                                                    ) {
                                                        $output = \json_encode($output);
                                                    }

                                                    echo $output;
                                                ?>
                                            </span>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php } ?>
    </div>
<?php
});
