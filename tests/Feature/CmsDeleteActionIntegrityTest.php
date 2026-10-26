<?php

namespace Tests\Feature;

use Tests\TestCase;

class CmsDeleteActionIntegrityTest extends TestCase
{
    private function bladeViews(): array
    {
        $files = glob(resource_path('views').'/{,*,*/*,*/*/*,*/*/*/*}{,*,*/*,*/*/*/*}.blade.php', GLOB_BRACE);

        sort($files);

        return array_values(array_filter($files, 'is_file'));
    }

    private function viewContents(): array
    {
        $contents = [];

        foreach ($this->bladeViews() as $file) {
            $contents[$file] = file_get_contents($file);
        }

        return $contents;
    }

    public function test_no_view_references_the_removed_perform_destroy_helper(): void
    {
        foreach ($this->viewContents() as $file => $content) {
            $this->assertStringNotContainsString(
                'performDestroy',
                $content,
                basename(dirname($file)).'/'.basename($file).' still calls the removed performDestroy() helper, so its delete button would throw.'
            );
        }
    }

    public function test_no_delete_control_uses_an_inline_javascript_handler(): void
    {
        foreach ($this->viewContents() as $file => $content) {
            $this->assertDoesNotMatchRegularExpression(
                '/<button[^>]*\bonclick="[^"]*[Dd]estroy[^"]*"/i',
                $content,
                basename(dirname($file)).'/'.basename($file).' uses an inline onclick delete handler instead of a CSRF-protected form.'
            );
        }
    }

    public function test_every_data_crud_delete_form_is_csrf_protected_and_uses_the_delete_verb(): void
    {
        $checked = 0;

        foreach ($this->viewContents() as $file => $content) {
            preg_match_all('/<form\b[^>]*data-crud-delete[^>]*>(.*?)<\/form>/is', $content, $matches);

            foreach ($matches[1] as $body) {
                $checked++;
                $label = basename(dirname($file)).'/'.basename($file);

                $this->assertStringContainsString('@csrf', $body, $label.' has a delete form without a CSRF token.');
                $this->assertStringContainsString("@method('DELETE')", $body, $label.' has a delete form that does not send the DELETE verb.');
            }
        }

        $this->assertGreaterThan(0, $checked, 'Expected at least one shared delete form to exist.');
    }

    public function test_shared_delete_javascript_still_exposes_the_helpers_the_markup_relies_on(): void
    {
        $javascript = file_get_contents(public_path('js/crud.js'));

        $this->assertStringContainsString('data-crud-delete', $javascript);
        $this->assertStringContainsString('function confirmDestroy', $javascript);
        $this->assertStringContainsString('function destroy', $javascript);
        $this->assertStringContainsString('.pagination', $javascript, 'Delete handling must reload paginated tables to avoid stale pagination.');
    }

    public function test_pagination_detection_is_anchored_to_the_table_not_the_row(): void
    {
        $javascript = file_get_contents(public_path('js/crud.js'));

        $this->assertStringNotContainsString(
            "row.closest('.pagination')",
            $javascript,
            'Laravel renders links() as a sibling of the table, so pagination can never be an ancestor of a <tr>.'
        );
        $this->assertStringContainsString(
            "row.closest('table')",
            $javascript,
            'Pagination must be resolved from the table so it can find a sibling .pagination element.'
        );
        $this->assertStringContainsString(
            'function tableIsPaginated',
            $javascript,
            'Pagination lookup must walk up the table ancestors, because links() renders outside .table-responsive.'
        );
        $this->assertStringContainsString(
            'while (node && node !== document.body)',
            $javascript,
            'The ancestor walk must climb past the table wrapper until it finds the pagination container.'
        );
    }

    public function test_paginated_tables_render_links_outside_the_table_element(): void
    {
        $found = false;

        foreach ($this->viewContents() as $file => $content) {
            if (preg_match('/<\/table>(?:(?!<\/table>).){0,400}?\{\{ \$[a-zA-Z_]+->links\(\) \}\}/s', $content)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue(
            $found,
            'Expected at least one view to render pagination links as a sibling after </table>, which is why the delete handler must resolve pagination from the table.'
        );
    }
}
