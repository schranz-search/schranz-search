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
 * @var array{
 *     query: string,
 *     query: string,
 * } $parameters
 * @var float $queryTime
 * @var \Schranz\Search\SEAL\Search\Result $result
 */
$base = require \dirname(__DIR__) . '/base.inc.php';

$base(function () use ($engines, $result, $parameters, $queryTime): void {
    ?>
    <div style="min-height: 100vh; background: #eee; position: relative;">
        <?php if (null !== $result) {
            $keys = [];
            $documents = [...$result];
            foreach ($documents as $document) {
                $keys = \array_merge($keys, \array_keys($document));
                $keys = \array_unique($keys);
            }
            ?>
            <header style="position: sticky; top: 0; padding: 24px; background: #eee; box-shadow: 0 0 12px rgba(0, 0, 0, 0.8);">
                <form id="search" method="get" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: end; gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label for="index" style="font-weight: bold; display: block;">
                                Index
                            </label>

                            <select id="index" name="index" style="border-radius: 4px; border: 2px solid #ccc; background: white; display: block; padding: 12px 12px;">
                                <?php foreach ($engines as $engineKey => $engine) { ?>
                                    <optgroup label="<?php echo $engine['title']; ?>">
                                        <?php foreach ($engine['indexes'] as $indexKey => $index) { ?>
                                            <option value="<?php echo $index['value']; ?>" <?php echo $index['active'] ? 'selected' : ''; ?>>
                                                <?php echo $index['title']; ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                            <label for="query" style="font-weight: bold; display: block;">
                                Query
                            </label>

                            <input autofocus type="search" name="query" id="query" value="<?php echo escape($parameters['query'] ?? ''); ?>" style="border-radius: 4px; border: 2px solid #ccc; display: blocK; padding: 12px 12px;">
                        </div>

                        <button type="submit" style="padding: 12px 24px; border: none; background: #fff; border: 2px solid #ccc; font-weight: bold; border-radius: 4px; width: auto;">
                            Search
                        </button>
                    </div>
                </form>
            </header>

            <main style="padding: 24px;">
                <h1 style="margin: 0;">
                    <?php if ($result->total() > 0) { ?>
                        Results (~<?php echo $result->total(); ?> Hits) <br />
                        <small style="font-size: 14px;">Query Time: <?php echo \number_format($queryTime, 4); ?>s</small>
                    <?php } else { ?>
                        No results
                    <?php } ?>
                </h1>

                <?php if ($result->total() > 0) { ?>
                    <div style="display: block; overflow-x: auto; scrollbar-width: thin;">
                        <table style="margin-top: 24px; width: 100%; text-align: left; border-spacing: 0 4px;">
                            <thead>
                                <tr style="box-shadow: 0 0 24px #ccc;">
                                    <?php foreach ($keys as $keyCount => $key) { ?>
                                        <?php
                                                $borderWidth = match (true) {
                                                    0 === $keyCount => '1px 0 1px 1px',
                                                    ($keyCount + 1) === \count($keys) => '1px 1px 1px 0',
                                                    default => '1px 0 1px 0',
                                                };
                                        $borderRadius = match (true) {
                                            0 === $keyCount => '4px 0 0 4px',
                                            ($keyCount + 1) === \count($keys) => '0 4px 4px 0',
                                            default => '0',
                                        };
                                        ?>
                                        <th style="text-transform: uppercase; padding: 12px 16px; background: white; border: none; border: 1px solid #333; border-radius: <?php echo $borderRadius; ?>; border-width: <?php echo $borderWidth; ?>;"><?php echo $key; ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $count = 0; ?>
                                <?php foreach ($documents as $document) { ?>
                                    <tr>
                                        <?php ++$count; ?>
                                        <?php foreach ($keys as $indexCount => $key) { ?>
                                            <?php
                                                $borderWidth = match (true) {
                                                    0 === $indexCount => '1px 0 1px 1px',
                                                    ($indexCount + 1) === \count($keys) => '1px 1px 1px 0',
                                                    default => '1px 0 1px 0',
                                                };
                                            $borderRadius = match (true) {
                                                0 === $indexCount => '4px 0 0 4px',
                                                ($indexCount + 1) === \count($keys) => '0 4px 4px 0',
                                                default => '0',
                                            };
                                            $borderColor = match (true) {
                                                0 === $count % 2 => '#999',
                                                default => '#999',
                                            };
                                            $background = match (true) {
                                                0 === $count % 2 => 'white',
                                                default => '#f3f3f3',
                                            };
                                            ?>
                                            <td style="padding: 12px 16px; border: none; border: 2px solid <?php echo $borderColor; ?>; border-radius: <?php echo $borderRadius; ?>; border-width: <?php echo $borderWidth; ?>; background: <?php echo $background; ?>;">
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
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>

                <!-- pagination for search with submit buttons -->
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
                    <?php $page = $parameters['page']; ?>
                    <?php $limit = $parameters['limit']; ?>
                    <?php $lastPage = \ceil($result->total() / $limit); ?>

                    <?php if ($page > 0) { ?>
                        <button form="search" type="submit" name="page" value="<?php echo $page - 1; ?>" style="cursor: pointer; padding: 4px 12px; width: auto; height: 40px; text-align: center; border: none; background: #fff; border: 2px solid #ccc; font-weight: bold; border-radius: 4px;">
                            Previous
                        </button>
                    <?php } ?>

                    <?php if ($page < $lastPage) { ?>
                        <button form="search" type="submit" name="page" value="<?php echo $page + 1; ?>" style="cursor: pointer; padding: 4px 12px; width: auto; height: 40px; text-align: center; border: none; background: #fff; border: 2px solid #ccc; font-weight: bold; border-radius: 4px;">
                            Next
                        </button>
                    <?php } ?>
                </div>
            </main>
        <?php } ?>
    </div>
<?php
});
